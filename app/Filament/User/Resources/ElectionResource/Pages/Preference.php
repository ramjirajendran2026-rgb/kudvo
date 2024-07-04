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
use App\Models\User;
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

    public static string|Alignment $formActionsAlignment = Alignment::End;

    public ?array $data = [];

    public function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if (empty($this->getElection()->plan_id)) {
            $this->redirect(Plan::getUrl(parameters: [$this->getElection()]));

            return;
        }

        $election = $this->getElection();
        $election->preference ??= new PreferenceData();

        $this->form->fill($election->attributesToArray());
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

    public static function getNavigationLabel(): string
    {
        return __('filament.user.election-resource.pages.preference.navigation_label');
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
            ->disabled(condition: ! $this->canSave() || ! $this->hasFullAccess())
            ->schema(components: [
                Group::make()
                    ->mutateDehydratedStateUsing(callback: fn (array $state): array => array_merge(
                        $state,
                        [
                            'mfa_sms_auto_fill_only' => ($state['mfa_sms'] ?? false) && ! ($state['mfa_mail'] ?? false) ?
                                ($state['mfa_sms_auto_fill_only'] ?? false) :
                                false,
                            'web_app_manifest' => ($state['web_app_manifest_enabled'] ?? false) ? $state['web_app_manifest'] : null,
                        ]
                    ))
                    ->columns()
                    ->columnSpanFull()
                    ->statePath(path: 'preference')
                    ->schema(components: [
                        Section::make(heading: __('filament.user.election-resource.pages.preference.form.ballot_access_section.heading'))
                            ->columnSpan(span: 1)
                            ->description(description: __('filament.user.election-resource.pages.preference.form.ballot_access_section.description'))
                            ->hidden(
                                condition: ! $plan->hasAnyFeature(features: [
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
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BallotAccessCommonLink))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.ballot_link_common.label'))
                                    ->rule(
                                        rule: fn (Field $component) => 'accepted_if:'.$component->getContainer()->getStatePath().'.ballot_link_unique,false'
                                    )
                                    ->validationMessages(messages: [
                                        'accepted_if' => __('filament.user.election-resource.pages.preference.form.ballot_link_common.validation.accepted_if'),
                                    ]),

                                FeatureToggle::make(name: 'ballot_link_unique')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotAccessUniqueLink),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotAccessUniqueLink),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotAccessUniqueLink),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BallotAccessUniqueLink))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.ballot_link_unique.label'))
                                    ->rule(
                                        rule: fn (Get $get) => function (string $attribute, bool $value, Closure $fail) use ($get): void {
                                            if (
                                                $value &&
                                                ! $get(path: 'ballot_link_mail') &&
                                                ! $get(path: 'ballot_link_sms')
                                            ) {
                                                $fail(__('filament.user.election-resource.pages.preference.form.ballot_link_unique.validation.custom_rule'));
                                            }
                                        }
                                    ),
                            ]),

                        Section::make(heading: __('filament.user.election-resource.pages.preference.form.ip_restriction_section.heading'))
                            ->columnSpan(span: 1)
                            ->description(description: __('filament.user.election-resource.pages.preference.form.ip_restriction_section.description'))
                            ->hidden(
                                condition: ! $plan->hasAnyFeature(features: [
                                    ElectionFeature::IpRestriction,
                                ])
                            )
                            ->schema(components: [
                                FeatureToggle::make(name: 'ip_restriction')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::IpRestriction),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::IpRestriction),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::IpRestriction),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->afterStateUpdated(callback: function (bool $state, Set $set): void {
                                        $set(path: 'ip_restriction_threshold', state: $state ? 1 : null);
                                    })
                                    ->dehydrated()
                                    ->formatStateUsing(callback: static fn (Get $get): bool => $get(path: 'ip_restriction_threshold') ?? false)
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::IpRestriction))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.ip_restriction.label'))
                                    ->live(),

                                Quantity::make(name: 'ip_restriction_threshold')
                                    ->dehydratedWhenHidden()
                                    ->dehydrateStateUsing(callback: fn (Get $get, ?int $state): ?int => $get('ip_restriction') ? ($state ?: 1) : null)
                                    ->formatStateUsing(callback: static fn (?int $state): ?int => $state ?: 1)
                                    ->hiddenLabel()
                                    ->heading(heading: __('filament.user.election-resource.pages.preference.form.ip_restriction_threshold.heading'))
                                    ->maxValue(value: 10000)
                                    ->minValue(value: 1)
                                    ->numeric()
                                    ->requiredIfAccepted(statePath: 'ip_restriction')
                                    ->visible(condition: static fn (Get $get): bool => $get(path: 'ip_restriction')),
                            ]),

                        Section::make(heading: __('filament.user.election-resource.pages.preference.form.ballot_link_delivery_section.heading'))
                            ->columnSpan(span: 1)
                            ->description(description: __('filament.user.election-resource.pages.preference.form.ballot_link_delivery_section.description'))
                            ->hidden(
                                condition: ! $plan->hasAnyFeature(features: [
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
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BallotLinkEmail))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.ballot_link_mail.label')),

                                FeatureToggle::make(name: 'ballot_link_sms')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotLinkSms),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotLinkSms),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotLinkSms),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BallotLinkSms))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.ballot_link_sms.label')),

                                FeatureToggle::make(name: 'ballot_link_whatsapp')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotLinkWhatsapp),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotLinkWhatsapp),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotLinkWhatsapp),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->disabled()
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BallotLinkWhatsapp))
                                    ->hint(hint: __('filament.user.election-resource.pages.preference.form.ballot_link_whatsapp.hint'))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.ballot_link_whatsapp.label')),
                            ]),

                        Section::make(heading: __('filament.user.election-resource.pages.preference.form.mfa_code_delivery_section.heading'))
                            ->columnSpan(span: 1)
                            ->description(description: __('filament.user.election-resource.pages.preference.form.mfa_code_delivery_section.description'))
                            ->hidden(
                                condition: ! $plan->hasAnyFeature(features: [
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
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::VerificationCodeEmail))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.mfa_mail.label'))
                                    ->live(),

                                FeatureToggle::make(name: 'mfa_sms')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::VerificationCodeSms),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::VerificationCodeSms),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::VerificationCodeSms),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->grow(condition: false)
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::VerificationCodeSms))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.mfa_sms.label'))
                                    ->live(),

                                FeatureToggle::make(name: 'mfa_sms_auto_fill_only')
                                    ->hintIcon(icon: 'heroicon-o-information-circle')
                                    ->hintIconTooltip(tooltip: __('filament.user.election-resource.pages.preference.form.mfa_sms_auto_fill_only.hint_icon.tooltip'))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.mfa_sms_auto_fill_only.label'))
                                    ->visible(condition: static fn (Get $get): bool => $get(path: 'mfa_sms') && ! $get(path: 'mfa_mail')),

                                FeatureToggle::make(name: 'mfa_whatsapp')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::VerificationCodeWhatsapp),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::VerificationCodeWhatsapp),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::VerificationCodeWhatsapp),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->disabled()
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::VerificationCodeWhatsapp))
                                    ->hint(hint: __('filament.user.election-resource.pages.preference.form.mfa_whatsapp.hint'))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.mfa_whatsapp.label')),
                            ]),

                        Section::make(heading: __('filament.user.election-resource.pages.preference.form.ballot_ack_section.heading'))
                            ->columnSpan(span: 1)
                            ->description(description: __('filament.user.election-resource.pages.preference.form.ballot_ack_section.description'))
                            ->hidden(
                                condition: ! $plan->hasAnyFeature(features: [
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
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BallotAcknowledgementEmail))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.voted_confirmation_mail.label')),

                                FeatureToggle::make(name: 'voted_confirmation_sms')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotAcknowledgementSms),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotAcknowledgementSms),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotAcknowledgementSms),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BallotAcknowledgementSms))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.voted_confirmation_sms.label')),

                                FeatureToggle::make(name: 'voted_confirmation_whatsapp')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotAcknowledgementWhatsapp),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotAcknowledgementWhatsapp),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotAcknowledgementWhatsapp),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->disabled()
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BallotAcknowledgementWhatsapp))
                                    ->hint(hint: __('filament.user.election-resource.pages.preference.form.voted_confirmation_whatsapp.hint'))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.voted_confirmation_whatsapp.label')),
                            ]),

                        Section::make(heading: __('filament.user.election-resource.pages.preference.form.ballot_copy_section.heading'))
                            ->columnSpan(span: 1)
                            ->description(description: __('filament.user.election-resource.pages.preference.form.ballot_copy_section.description'))
                            ->hidden(
                                condition: ! $plan->hasAnyFeature(features: [
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
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BallotCopyDownload))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.voted_ballot_download.label')),

                                FeatureToggle::make(name: 'voted_ballot_mail')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotCopyEmail),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotCopyEmail),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotCopyEmail),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BallotCopyEmail))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.voted_ballot_mail.label')),

                                FeatureToggle::make(name: 'voted_ballot_whatsapp')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::BallotCopyWhatsapp),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::BallotCopyWhatsapp),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::BallotCopyWhatsapp),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->disabled()
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BallotCopyWhatsapp))
                                    ->hint(hint: __('filament.user.election-resource.pages.preference.form.voted_ballot_whatsapp.hint'))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.voted_ballot_whatsapp.label')),
                            ]),

                        Section::make(heading: __('filament.user.election-resource.pages.preference.form.security_preference_section.heading'))
                            ->columns()
                            ->description(description: __('filament.user.election-resource.pages.preference.form.security_preference_section.description'))
                            ->hidden(
                                condition: ! $plan->hasAnyFeature(features: [
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
                                                feeCurrency: $plan->currency,
                                                hideAddOnPrice: ! $this->canSave(),
                                            )
                                            ->afterStateUpdated(callback: function (bool $state, Set $set): void {
                                                if (! $state) {
                                                    return;
                                                }

                                                $set(path: 'voted_ballot_update', state: false);
                                            })
                                            ->default(state: true)
                                            ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::TrackableVotes))
                                            ->helperText(text: __('filament.user.election-resource.pages.preference.form.dnt_votes.helper_text'))
                                            ->label(label: __('filament.user.election-resource.pages.preference.form.dnt_votes.label'))
                                            ->live(),

                                        FeatureToggle::make(name: 'voted_ballot_update')
                                            ->addOn(
                                                condition: $plan->hasAddOnFeature(feature: ElectionFeature::EditableVotes),
                                                featureFee: $plan->getFeatureFee(feature: ElectionFeature::EditableVotes),
                                                electorFee: $plan->getElectorFee(feature: ElectionFeature::EditableVotes),
                                                feeCurrency: $plan->currency,
                                                hideAddOnPrice: ! $this->canSave(),
                                            )
                                            ->helperText(text: __('filament.user.election-resource.pages.preference.form.voted_ballot_update.helper_text'))
                                            ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::EditableVotes))
                                            ->label(label: __('filament.user.election-resource.pages.preference.form.voted_ballot_update.label'))
                                            ->disabled(condition: fn (Get $get): bool => $get(path: 'dnt_votes')),
                                    ]),

                                FeatureToggle::make(name: 'prevent_duplicate_device')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::DeviceRestriction),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::DeviceRestriction),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::DeviceRestriction),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->helperText(text: __('filament.user.election-resource.pages.preference.form.prevent_duplicate_device.helper_text'))
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::DeviceRestriction))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.prevent_duplicate_device.label')),
                            ]),

                        Section::make(heading: __('filament.user.election-resource.pages.preference.form.elector_preference_section.heading'))
                            ->columns()
                            ->hidden(
                                condition: ! $plan->hasAnyFeature(features: [
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
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->helperText(text: __('filament.user.election-resource.pages.preference.form.elector_duplicate_email.helper_text'))
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::ElectorEmailUnique))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.elector_duplicate_email.label')),

                                FeatureToggle::make(name: 'elector_duplicate_phone')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::ElectorPhoneUnique),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::ElectorPhoneUnique),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::ElectorPhoneUnique),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->helperText(text: __('filament.user.election-resource.pages.preference.form.elector_duplicate_phone.helper_text'))
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::ElectorPhoneUnique))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.elector_duplicate_phone.label')),

                                FeatureToggle::make(name: 'elector_update_after_publish')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::ElectorCorrections),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::ElectorCorrections),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::ElectorCorrections),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->helperText(text: __('filament.user.election-resource.pages.preference.form.elector_update_after_publish.helper_text'))
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::ElectorCorrections))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.elector_update_after_publish.label')),
                            ]),

                        Section::make(heading: __('filament.user.election-resource.pages.preference.form.candidate_preference_section.heading'))
                            ->columns()
                            ->hidden(
                                condition: ! $plan->hasAnyFeature(features: [
                                    ElectionFeature::CandidatePhoto,
                                    ElectionFeature::CandidateSymbol,
                                    ElectionFeature::CandidateTeam,
                                ])
                            )
                            ->schema(components: [
                                Select::make(name: 'candidate_sort')
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.candidate_sort.label'))
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
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::CandidatePhoto))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.candidate_photo.label')),

                                FeatureToggle::make(name: 'candidate_symbol')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::CandidateSymbol),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::CandidateSymbol),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::CandidateSymbol),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::CandidateSymbol))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.candidate_symbol.label')),

                                FeatureToggle::make(name: 'candidate_bio')
                                    ->hidden()
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.candidate_bio.label')),

                                FeatureToggle::make(name: 'candidate_attachment')
                                    ->hidden()
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.candidate_attachment.label')),

                                FeatureToggle::make(name: 'candidate_group')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::CandidateTeam),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::CandidateTeam),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::CandidateTeam),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::CandidateTeam) || true)
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.candidate_group.label')),
                            ]),

                        Section::make(heading: __('filament.user.election-resource.pages.preference.form.advanced_preferences_section.heading'))
                            ->collapsed()
                            ->description(description: __('filament.user.election-resource.pages.preference.form.advanced_preferences_section.description'))
                            ->hidden(
                                condition: ! $plan->hasAnyFeature(features: [
                                    ElectionFeature::SegmentedVoting,
                                    ElectionFeature::BoothVoting,
                                    ElectionFeature::DisableUnopposedSelection,
                                ])
                            )
                            ->schema(components: [
                                FeatureToggle::make(name: 'disable_unopposed_selection')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::DisableUnopposedSelection),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::DisableUnopposedSelection),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::DisableUnopposedSelection),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::DisableUnopposedSelection))
                                    ->label(label: 'Restrict selection for Unopposed candidates'),

                                FeatureToggle::make(name: 'segmented_ballot')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::SegmentedVoting),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::SegmentedVoting),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::SegmentedVoting),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->helperText(text: __('filament.user.election-resource.pages.preference.form.segmented_ballot.helper_text'))
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::SegmentedVoting))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.segmented_ballot.label')),

                                FeatureToggle::make(name: 'booth_voting')
                                    ->addOn(
                                        condition: $plan->hasAddOnFeature(feature: ElectionFeature::BoothVoting),
                                        featureFee: $plan->getFeatureFee(feature: ElectionFeature::BoothVoting),
                                        electorFee: $plan->getElectorFee(feature: ElectionFeature::BoothVoting),
                                        feeCurrency: $plan->currency,
                                        hideAddOnPrice: ! $this->canSave(),
                                    )
                                    ->hidden(condition: ! $plan->hasFeature(feature: ElectionFeature::BoothVoting))
                                    ->label(label: __('filament.user.election-resource.pages.preference.form.booth_voting.label'))
                                    ->live(),

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
                                    ->hint(hint: 'Experimental')
                                    ->hintIcon(
                                        icon: 'heroicon-o-information-circle',
                                        tooltip: 'This is experimental and may not work as expected. Please use with caution.'
                                    )
                                    ->label(label: 'Enable PWA for booth voting')
                                    ->live()
                                    ->visible(condition: Filament::auth()->user()->canAccessPanel(panel: Filament::getPanel(id: 'admin'))),

                                Group::make()
                                    ->columns()
                                    ->columnSpanFull()
                                    ->statePath(path: 'web_app_manifest')
                                    ->visible(condition: static fn (Get $get): bool => $get(path: 'web_app_manifest_enabled'))
                                    ->schema(components: [
                                        TextInput::make(name: 'name')
                                            ->charCounter(count: 80)
                                            ->label(label: 'Name')
                                            ->maxLength(length: 80)
                                            ->required(),

                                        TextInput::make(name: 'short_name')
                                            ->charCounter(count: 12)
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
                    ]),

                Section::make(heading: __('filament.user.election-resource.pages.preference.form.booth_voting_section.heading'))
                    ->collapsed()
                    ->columns()
                    ->description(description: __('filament.user.election-resource.pages.preference.form.booth_voting_section.description'))
                    ->statePath(path: 'booth_preference')
                    ->visible(condition: fn (Get $get): bool => $get('preference.booth_voting'))
                    ->schema(components: [
                        Toggle::make(name: 'login_by_admin'),

                        Toggle::make(name: 'login_by_self'),

                        Toggle::make(name: 'logout_by_admin'),

                        Toggle::make(name: 'logout_by_self'),

                        Toggle::make(name: 'voted_ballot_print_by_admin'),

                        Toggle::make(name: 'voted_ballot_print_by_self'),

                        Toggle::make(name: 'voted_ballot_auto_print'),

                        Toggle::make(name: 'flash_voted_ballot'),
                    ]),
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
            ->label(label: __('filament.user.election-resource.pages.preference.actions.save.label'))
            ->submit(form: 'save')
            ->visible(condition: $this->canSave() && $this->hasFullAccess());
    }

    public function getChangePlanAction(): Action
    {
        return Action::make(name: 'changePlan')
            ->label(label: __('filament.user.election-resource.pages.preference.actions.change_plan.label'))
            ->url(url: Plan::getUrl(parameters: [$this->getElection()]));
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage($election) &&
            static::can(action: 'viewPreference', election: $election);
    }

    protected function canSave(): bool
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        return $user->canAccessPanel(panel: Filament::getPanel(id: 'admin')) ||
            static::can(action: 'savePreference', election: $this->getElection());
    }

    public function save(): void
    {
        abort_unless(boolean: $this->canSave(), code: 403);

        $data = $this->form->getState();

        $this->getElection()->update(attributes: $data);

        Notification::make()
            ->success()
            ->title(title: __('filament.user.election-resource.pages.preference.actions.save.success_notification.title'))
            ->send();

        $this->redirect(url: $this->getRedirectUrl());
    }
}
