<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Data\Election\PreferenceData;
use App\Enums\CandidateSort;
use App\Enums\ElectionCollaboratorPermission;
use App\Enums\ElectionFeature;
use App\Enums\ElectionSetupStep;
use App\Forms\Components\FeatureToggle;
use App\Models\Election;
use App\Models\ElectionPlan;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LaraZeus\Quantity\Components\Quantity;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Preference extends ElectionPage
{
    use InteractsWithFormActions;

    protected static string $view = 'filament.user.resources.election-resource.pages.preference';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $activeNavigationIcon = 'heroicon-s-cog-6-tooth';

    public static string | Alignment $formActionsAlignment = Alignment::End;

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $election = $this->getElection();
        $election->preference ??= new PreferenceData();

        $this->form->fill($election->attributesToArray());
    }

    public function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if (empty($this->getElection()->plan_id)) {
            $this->redirect(Plan::getUrl(parameters: [$this->getElection()]));
        }
    }

    public function hasReadAccess(): bool
    {
        return $this->isOwner()
            || $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->preference !== ElectionCollaboratorPermission::NoAccess;
    }

    public function hasFullAccess(): bool
    {
        return $this->isOwner()
            || $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->preference === ElectionCollaboratorPermission::FullAccess;
    }

    public function getCurrentStep(): ?ElectionSetupStep
    {
        return ElectionSetupStep::Preference;
    }

    public function shouldShowPricingTable(): bool
    {
        return blank($this->getElection()->plan_id);
    }

    public function getPlans(): Collection
    {
        return ElectionPlan::where('currency', 'inr')->oldest(column: 'sort')->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),

            $this->getChangePlanAction(),

            ActionGroup::make(actions: [
                $this->getCollaboratorsPageAction(),
            ])->dropdownPlacement(placement: 'bottom-end'),
        ];
    }

    public function form(Form $form): Form
    {
        $plan = $this->getElection()->plan;
        if (blank($plan)) {
            return $form;
        }

        return $form
            ->disabled(condition: !$this->canSave() || !$this->hasFullAccess())
            ->schema(components: [
                Group::make()
                    ->mutateDehydratedStateUsing(callback: fn (array $state): array => array_merge(
                        $state,
                        [
                            'mfa_sms_auto_fill_only' => ($state['mfa_sms'] ?? false) && !($state['mfa_mail'] ?? false) ?
                                ($state['mfa_sms_auto_fill_only'] ?? false) :
                                false,
                            'web_app_manifest' => ($state['web_app_manifest_enabled'] ?? false) ? $state['web_app_manifest'] : null,
                        ]
                    ))
                    ->columns()
                    ->columnSpanFull()
                    ->statePath(path: 'preference')
                    ->schema(
                        components: [
                            Section::make(heading:'Ballot Access')
                                ->columnSpan(span: 1)
                                ->description(description: 'Electors will be able to access their eligible ballot by these options')
                                ->hidden(
                                    condition: !$plan->hasAnyFeature(features: [
                                        ElectionFeature::BallotAccessCommonLink,
                                        ElectionFeature::BallotAccessUniqueLink,
                                    ])
                                )
                                ->schema(components: [
                                    FeatureToggle::make(name: 'ballot_link_common')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotAccessCommonLink),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotAccessCommonLink),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotAccessCommonLink),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BallotAccessCommonLink))
                                        ->rule(
                                            rule: fn (Field $component) => 'accepted_if:'.$component->getContainer()->getStatePath().'.ballot_link_unique,false'
                                        )
                                        ->validationMessages(messages: [
                                            'accepted_if' => 'This must be enabled when unique link is disabled'
                                        ]),

                                    FeatureToggle::make(name: 'ballot_link_unique')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotAccessUniqueLink),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotAccessUniqueLink),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotAccessUniqueLink),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BallotAccessUniqueLink))
                                        ->label(label: 'Unique link')
                                        ->rule(
                                            rule: fn (Get $get) => function (string $attribute, bool $value, Closure $fail) use ($get): void{
                                                if (
                                                    $value &&
                                                    !$get(path: 'ballot_link_mail') &&
                                                    !$get(path: 'ballot_link_sms')
                                                ) {
                                                    $fail('When this is enabled, at least one delivery method must be enabled');
                                                }
                                            }
                                        ),
                                ]),

                            Section::make(heading: 'IP Restriction')
                                ->columnSpan(span: 1)
                                ->description(description: 'Restrict electors voting from same IP address')
                                ->hidden(
                                    condition: !$plan->hasAnyFeature(features: [
                                        ElectionFeature::IpRestriction,
                                    ])
                                )
                                ->schema(components: [
                                    FeatureToggle::make(name: 'ip_restriction')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::IpRestriction),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::IpRestriction),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::IpRestriction),
                                            feeCurrency: $plan->currency
                                        )
                                        ->afterStateUpdated(callback: function (bool $state, Set $set): void {
                                            $set(path: 'ip_restriction_threshold', state: $state ? 1 : null);
                                        })
                                        ->dehydrated()
                                        ->formatStateUsing(callback: static fn (Get $get): bool => $get(path: 'ip_restriction_threshold') ?? false)
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::IpRestriction))
                                        ->label(label: 'Enable')
                                        ->live(),

                                    Quantity::make(name: 'ip_restriction_threshold')
                                        ->formatStateUsing(callback: static fn (?int $state): ?int => $state ?: 1)
                                        ->hiddenLabel()
                                        ->heading(heading: 'Max. votes')
                                        ->maxValue(value: 10000)
                                        ->minValue(value: 1)
                                        ->numeric()
                                        ->required()
                                        ->visible(condition: static fn (Get $get): bool => $get(path: 'ip_restriction')),
                                ]),

                            Section::make(heading:'Ballot Link Delivery')
                                ->columnSpan(span: 1)
                                ->description(description: 'Electors will receive their ballot link through these medium.')
                                ->hidden(
                                    condition: !$plan->hasAnyFeature(features: [
                                        ElectionFeature::BallotLinkEmail,
                                        ElectionFeature::BallotLinkSms,
                                        ElectionFeature::BallotLinkWhatsapp,
                                    ])
                                )
                                ->schema(components: [
                                    FeatureToggle::make(name: 'ballot_link_mail')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotLinkEmail),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotLinkEmail),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotLinkEmail),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BallotLinkEmail))
                                        ->label(label: 'Email'),

                                    FeatureToggle::make(name: 'ballot_link_sms')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotLinkSms),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotLinkSms),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotLinkSms),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BallotLinkSms))
                                        ->label(label: 'SMS'),

                                    FeatureToggle::make(name: 'ballot_link_whatsapp')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotLinkWhatsapp),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotLinkWhatsapp),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotLinkWhatsapp),
                                            feeCurrency: $plan->currency
                                        )
                                        ->disabled()
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BallotLinkWhatsapp))
                                        ->hint(hint: 'Coming soon')
                                        ->label(label: 'Whatsapp'),
                                ]),

                            Section::make(heading: 'MFA Code Delivery')
                                ->columnSpan(span: 1)
                                ->description(description: 'Electors will receive MFA (Multi-Factor Authentication) code through these medium. This will be used to verify the elector\'s identity before submitting their votes.')
                                ->hidden(
                                    condition: !$plan->hasAnyFeature(features: [
                                        ElectionFeature::VerificationCodeEmail,
                                        ElectionFeature::VerificationCodeSms,
                                        ElectionFeature::VerificationCodeWhatsapp,
                                    ])
                                )
                                ->schema(components: [
                                    FeatureToggle::make(name: 'mfa_mail')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::VerificationCodeEmail),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::VerificationCodeEmail),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::VerificationCodeEmail),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::VerificationCodeEmail))
                                        ->label(label: 'Email')
                                        ->live(),

                                    Split::make(schema: [
                                        FeatureToggle::make(name: 'mfa_sms')
                                            ->addOn(
                                                condition: $plan->hasAddOnFeature(feature: ElectionFeature::VerificationCodeSms),
                                                featureFee: $plan->getFeatureFee(feature: ElectionFeature::VerificationCodeSms),
                                                electorFee: $plan->getElectorFee(feature: ElectionFeature::VerificationCodeSms),
                                                feeCurrency: $plan->currency
                                            )
                                            ->grow(condition: false)
                                            ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::VerificationCodeSms))
                                            ->label(label: 'SMS')
                                            ->live(),

                                        FeatureToggle::make(name: 'mfa_sms_auto_fill_only')
                                            ->hintIcon(icon: 'heroicon-o-information-circle')
                                            ->hintIconTooltip(tooltip: 'Supports only on Android (Chrome) and iOS (Safari) devices. Voting from other devices will be restricted.')
                                            ->label(label: 'Prevent manual entry')
                                            ->visible(condition: static fn (Get $get): bool => $get(path: 'mfa_sms') && !$get(path: 'mfa_mail')),
                                    ])
                                        ->hidden(
                                            condition: !$plan->hasAnyFeature(features: [
                                                ElectionFeature::VerificationCodeSms,
                                            ])
                                        )
                                        ->label(label: 'SMS'),

                                    FeatureToggle::make(name: 'mfa_whatsapp')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::VerificationCodeWhatsapp),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::VerificationCodeWhatsapp),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::VerificationCodeWhatsapp),
                                            feeCurrency: $plan->currency
                                        )
                                        ->disabled()
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::VerificationCodeWhatsapp))
                                        ->hint(hint: 'Coming soon')
                                        ->label(label: 'Whatsapp'),
                                ]),

                            Section::make(heading: 'Ballot Acknowledgement')
                                ->columnSpan(span: 1)
                                ->description(description: 'Electors will receive confirmation of their votes through these medium.')
                                ->hidden(
                                    condition: !$plan->hasAnyFeature(features: [
                                        ElectionFeature::BallotAcknowledgementEmail,
                                        ElectionFeature::BallotAcknowledgementSms,
                                        ElectionFeature::BallotAcknowledgementWhatsapp,
                                    ])
                                )
                                ->schema(components: [
                                    FeatureToggle::make(name: 'voted_confirmation_mail')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotAcknowledgementEmail),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotAcknowledgementEmail),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotAcknowledgementEmail),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BallotAcknowledgementEmail))
                                        ->label(label: 'Email'),

                                    FeatureToggle::make(name: 'voted_confirmation_sms')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotAcknowledgementSms),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotAcknowledgementSms),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotAcknowledgementSms),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BallotAcknowledgementSms))
                                        ->label(label: 'SMS'),

                                    FeatureToggle::make(name: 'voted_confirmation_whatsapp')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotAcknowledgementWhatsapp),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotAcknowledgementWhatsapp),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotAcknowledgementWhatsapp),
                                            feeCurrency: $plan->currency
                                        )
                                        ->disabled()
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BallotAcknowledgementWhatsapp))
                                        ->hint(hint: 'Coming soon')
                                        ->label(label: 'Whatsapp'),
                                ]),

                            Section::make(heading: 'Sharing of Electors\'s Ballot Copy')
                                ->columnSpan(span: 1)
                                ->description(description: 'Electors will be able to share their voted ballot copy through these medium.')
                                ->hidden(
                                    condition: !$plan->hasAnyFeature(features: [
                                        ElectionFeature::BallotCopyDownload,
                                        ElectionFeature::BallotCopyEmail,
                                        ElectionFeature::BallotCopyWhatsapp,
                                    ])
                                )
                                ->schema(components: [
                                    FeatureToggle::make(name: 'voted_ballot_download')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotCopyDownload),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotCopyDownload),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotCopyDownload),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BallotCopyDownload))
                                        ->label(label: 'Direct download'),

                                    FeatureToggle::make(name: 'voted_ballot_mail')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotCopyEmail),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotCopyEmail),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotCopyEmail),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BallotCopyEmail))
                                        ->label(label: 'Email'),

                                    FeatureToggle::make(name: 'voted_ballot_whatsapp')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotCopyWhatsapp),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotCopyWhatsapp),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotCopyWhatsapp),
                                            feeCurrency: $plan->currency
                                        )
                                        ->disabled()
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BallotCopyWhatsapp))
                                        ->hint(hint: 'Coming soon')
                                        ->label(label: 'Whatsapp'),
                                ]),

                            Section::make(heading: 'Security preference')
                                ->columns()
                                ->description(description: 'These are security preferences that you can enable for your election.')
                                ->hidden(
                                    condition: !$plan->hasAnyFeature(features: [
                                        ElectionFeature::TrackableVotes,
                                        ElectionFeature::EditableVotes,
                                        ElectionFeature::DeviceRestriction,
                                    ])
                                )
                                ->schema(components: [
                                    Group::make()
                                        ->columns()
                                        ->columnSpanFull()
                                        ->schema(components: [
                                            FeatureToggle::make(name: 'dnt_votes')
                                                ->addOn(
                                                    condition: $plan->hasAddOnFeature(feature: ElectionFeature::TrackableVotes),
                                                    featureFee: $plan->getFeatureFee(feature: ElectionFeature::TrackableVotes),
                                                    electorFee: $plan->getElectorFee(feature: ElectionFeature::TrackableVotes),
                                                    feeCurrency: $plan->currency
                                                )
                                                ->afterStateUpdated(callback: function (bool $state, Set $set): void {
                                                    if (! $state) {
                                                        return;
                                                    }

                                                    $set(path: 'voted_ballot_update', state: false);
                                                })
                                                ->default(state: true)
                                                ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::TrackableVotes))
                                                ->helperText(text: 'This will prevent the system from tracking the electors\' votes.')
                                                ->label(label: 'Do Not Track electors\'s votes')
                                                ->live(),

                                            FeatureToggle::make(name: 'voted_ballot_update')
                                                ->addOn(
                                                    condition: $plan->hasAddOnFeature(feature: ElectionFeature::EditableVotes),
                                                    featureFee: $plan->getFeatureFee(feature: ElectionFeature::EditableVotes),
                                                    electorFee: $plan->getElectorFee(feature: ElectionFeature::EditableVotes),
                                                    feeCurrency: $plan->currency
                                                )
                                                ->helperText(text: 'This will allow electors to update their voted ballot.')
                                                ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::EditableVotes))
                                                ->label(label: 'Editable votes')
                                                ->disabled(condition: fn (Get $get): bool => $get(path: 'dnt_votes')),
                                        ]),

                                    FeatureToggle::make(name: 'prevent_duplicate_device')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::DeviceRestriction),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::DeviceRestriction),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::DeviceRestriction),
                                            feeCurrency: $plan->currency
                                        )
                                        ->label(label: 'Prevent duplicate device')
                                        ->helperText(text: 'This will prevent electors from casting votes from same device.')
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::DeviceRestriction))
                                        ->hint(hint: 'Experimental')
                                        ->hintIcon(
                                            icon: 'heroicon-o-information-circle',
                                            tooltip: 'This is experimental and may not work as expected. Please use with caution.'
                                        ),
                                ]),

                            Section::make(heading: 'Elector preference')
                                ->columns()
                                ->hidden(
                                    condition: !$plan->hasAnyFeature(features: [
                                        ElectionFeature::ElectorEmailUnique,
                                        ElectionFeature::ElectorPhoneUnique,
                                        ElectionFeature::ElectorCorrections,
                                    ])
                                )
                                ->schema(components: [
                                    FeatureToggle::make(name: 'elector_duplicate_email')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::ElectorEmailUnique),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::ElectorEmailUnique),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::ElectorEmailUnique),
                                            feeCurrency: $plan->currency
                                        )
                                        ->helperText(text: 'This will allow you to add multiple electors with same email address.')
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::ElectorEmailUnique))
                                        ->label(label: 'Duplicate email addresses'),

                                    FeatureToggle::make(name: 'elector_duplicate_phone')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::ElectorPhoneUnique),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::ElectorPhoneUnique),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::ElectorPhoneUnique),
                                            feeCurrency: $plan->currency
                                        )
                                        ->helperText(text: 'This will allow you to add multiple electors with same phone number.')
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::ElectorPhoneUnique))
                                        ->label(label: 'Duplicate phone numbers'),

                                    FeatureToggle::make(name: 'elector_update_after_publish')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::ElectorCorrections),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::ElectorCorrections),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::ElectorCorrections),
                                            feeCurrency: $plan->currency
                                        )
                                        ->helperText(text: 'This will allow you to update the elector details after the election is published.')
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::ElectorCorrections))
                                        ->label(label: 'Allow elector update after publish'),
                                ]),

                            Section::make(heading: 'Candidate preference')
                                ->columns()
                                ->hidden(
                                    condition: !$plan->hasAnyFeature(features: [
                                        ElectionFeature::CandidatePhoto,
                                        ElectionFeature::CandidateSymbol,
                                        ElectionFeature::CandidateTeam,
                                    ])
                                )
                                ->schema(components: [
                                    Select::make(name: 'candidate_sort')
                                        ->label(label: 'Display order')
                                        ->columnSpanFull()
                                        ->default(state: CandidateSort::MANUAL->value)
                                        ->dehydrated(condition: fn (?string $state): bool => filled($state))
                                        ->enum(enum: CandidateSort::class)
                                        ->columns(columns: 6)
                                        ->native(condition: false)
                                        ->options(options: Arr::mapWithKeys(
                                            array: CandidateSort::cases(),
                                            callback: fn (CandidateSort $case): array => [$case->value => $case->getLabel()]
                                        )),

                                    FeatureToggle::make(name: 'candidate_photo')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::CandidatePhoto),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::CandidatePhoto),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::CandidatePhoto),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::CandidatePhoto))
                                        ->label(label: 'Candidate photo'),

                                    FeatureToggle::make(name: 'candidate_symbol')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::CandidateSymbol),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::CandidateSymbol),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::CandidateSymbol),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::CandidateSymbol))
                                        ->label(label: 'Candidate symbol'),

                                    FeatureToggle::make(name: 'candidate_bio')
                                        ->hidden()
                                        ->label(label: 'Candidate bio text'),

                                    FeatureToggle::make(name: 'candidate_attachment')
                                        ->hidden()
                                        ->label(label: 'Candidate attachments'),

                                    FeatureToggle::make(name: 'candidate_group')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::CandidateTeam),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::CandidateTeam),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::CandidateTeam),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::CandidateTeam))
                                        ->label(label: 'Candidate group'),
                                ]),

                            Section::make(heading: 'Advanced preferences')
                                ->collapsed()
                                ->description(description: 'These are advanced preferences that you can enable for your election.')
                                ->hidden(
                                    condition: !$plan->hasAnyFeature(features: [
                                        ElectionFeature::SegmentedVoting,
                                    ])
                                )
                                ->schema(components: [
                                    FeatureToggle::make(name: 'segmented_ballot')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::SegmentedVoting),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::SegmentedVoting),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::SegmentedVoting),
                                            feeCurrency: $plan->currency
                                        )
                                        ->helperText(text: 'This will allow you to segment the ballot based on the elector details.')
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::SegmentedVoting))
                                        ->label(label: 'Segmented ballot'),
                                ]),

                            Section::make(heading: 'Booth voting')
                                ->collapsed()
                                ->description(description: 'This will allow electors to cast votes from a specific location. This is useful for conducting elections in a physical location.')
                                ->hidden(
                                    condition: !$plan->hasAnyFeature(features: [
                                        ElectionFeature::BoothVoting,
                                    ])
                                )
                                ->schema(components: [
                                    FeatureToggle::make(name: 'booth_voting')
                                        ->addOn(
                                            condition: $plan->hasAddOnFeature(feature: ElectionFeature::BoothVoting),
                                            featureFee: $plan->getFeatureFee(feature: ElectionFeature::BoothVoting),
                                            electorFee: $plan->getElectorFee(feature: ElectionFeature::BoothVoting),
                                            feeCurrency: $plan->currency
                                        )
                                        ->hidden(condition: !$plan->hasFeature(feature: ElectionFeature::BoothVoting))
                                        ->label(label: 'Enable booth voting'),

                                    Toggle::make(name: 'web_app_manifest_enabled')
                                        ->afterStateUpdated(callback: function (bool $state, Set $set, Get $get, self $livewire): void {
                                            $set(
                                                path: 'web_app_manifest',
                                                state: $state ? [
                                                    'name' => $livewire->getElection()->name,
                                                    'short_name' => $livewire->getElection()->name,
                                                    'icons' => [(string) Str::uuid() => []],
                                                ] : null
                                            );
                                        })
                                        ->formatStateUsing(callback: static fn (Election $record): bool => filled($record->preference?->web_app_manifest))
                                        ->helperText(text: 'This will allow you to add the election to the home screen of your booth devices.')
                                        ->hidden()
                                        ->hint(hint: 'Experimental')
                                        ->hintIcon(
                                            icon: 'heroicon-o-information-circle',
                                            tooltip: 'This is experimental and may not work as expected. Please use with caution.'
                                        )
                                        ->label(label: 'Enable PWA for booth voting')
                                        ->live(),

                                    Group::make()
                                        ->columns()
                                        ->columnSpanFull()
                                        ->statePath(path: 'web_app_manifest')
                                        ->visible(condition: static fn (Get $get): bool => $get(path: 'web_app_manifest_enabled'))
                                        ->schema(components: [
                                            TextInput::make(name: 'name')
                                                ->label(label: 'Name')
                                                ->maxLength(length: 100)
                                                ->required(),

                                            TextInput::make(name: 'short_name')
                                                ->label(label: 'Short name')
                                                ->maxLength(length: 12)
                                                ->required(),

                                            Repeater::make(name: 'icons')
                                                ->addable(condition: false)
                                                ->columns()
                                                ->columnSpanFull()
                                                ->defaultItems(count: 1)
                                                ->deletable(condition: false)
                                                ->hiddenLabel()
                                                ->reorderable(condition: false)
                                                ->schema(components: [
                                                    FileUpload::make(name: 'src')
                                                        ->afterStateUpdated(callback: function (BaseFileUpload $component, $state, Set $set): void {
                                                            if ($state instanceof TemporaryUploadedFile) {
                                                                $set(path: 'type', state: $state->getMimeType());

                                                                $dimensions = $state->dimensions();
                                                                $set(path: 'sizes', state: $dimensions[0].'x'.$dimensions[1]);

                                                                return;
                                                            }

                                                            if (blank($state)) {
                                                                return;
                                                            }

                                                            if (is_array($state)) {
                                                                return;
                                                            }

                                                            $component->state([(string) Str::uuid() => $state]);
                                                        })
                                                        ->directory(directory: 'election/pwa-icons')
                                                        ->getUploadedFileNameForStorageUsing(
                                                            callback: fn (TemporaryUploadedFile $file, self $livewire, Get $get): string => $livewire->getElection()->code.'-'.$get(path: 'sizes').'.'.$file->guessExtension()
                                                        )
                                                        ->image()
                                                        ->imageEditor()
                                                        ->imageEditorAspectRatios(ratios: ['1:1'])
                                                        ->imageCropAspectRatio('1:1')
                                                        ->imageResizeMode('cover')
                                                        ->imageResizeTargetHeight('512')
                                                        ->imageResizeTargetWidth('512')
                                                        ->label(label: 'Icon')
                                                        ->live()
                                                        ->openable()
                                                        ->required(),

                                                    Group::make()
                                                        ->schema(components: [
                                                            TextInput::make(name: 'sizes')
                                                                ->distinct()
                                                                ->helperText(text: 'Supported sizes: 512x512')
                                                                ->hint(hint: 'Fetched from the uploaded icon')
                                                                ->in(values: ['512x512'])
                                                                ->label(label: 'Icon sizes')
                                                                ->readOnly()
                                                                ->required(),

                                                            TextInput::make(name: 'type')
                                                                ->helperText(text: 'Supported formats: PNG, JPEG, WebP')
                                                                ->hint(hint: 'Fetched from the uploaded icon')
                                                                ->in(values: ['image/png', 'image/jpeg', 'image/webp'])
                                                                ->readOnly()
                                                                ->required(),
                                                        ]),
                                                ]),
                                        ]),
                                ]),
                        ]
                    ),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                form: $this->makeForm()
                    ->operation(operation: 'edit')
                    ->model(model: $this->getElection())
                    ->statePath(path: 'data')
            ),
        ];
    }

    public function getFormActions(): array
    {
        return [
            $this->getSaveAction(),
        ];
    }

    protected function getSaveAction(): Action
    {
        return Action::make(name: 'save')
            ->keyBindings(bindings: ['mod+s'])
            ->label(label: 'Save Preference')
            ->submit(form: 'save')
            ->visible(condition: $this->canSave() && $this->hasFullAccess());
    }

    public function getChangePlanAction(): Action
    {
        return Action::make(name: 'changePlan')
            ->url(url: Plan::getUrl(parameters: [$this->getElection()]));
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage($election) &&
            static::can(action: 'viewPreference', election: $election);
    }

    protected function canSave(): bool
    {
        return static::can(action: 'savePreference', election: $this->getElection());
    }

    public function save(): void
    {
        abort_unless(boolean: $this->canSave(), code: 403);

        $data = $this->form->getState();

        $this->getElection()->update(attributes: $data);

        Notification::make()
            ->success()
            ->title(title: 'Saved')
            ->send();

        $this->redirect(url: Dashboard::getUrl(parameters: [$this->getElection()]));
    }
}
