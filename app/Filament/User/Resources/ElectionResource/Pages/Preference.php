<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Data\Election\PreferenceData;
use App\Enums\CandidateSort;
use App\Models\Election;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Arr;
use LaraZeus\Quantity\Components\Quantity;

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

    public function form(Form $form): Form
    {
        return $form
            ->disabled(condition: !$this->canSave())
            ->schema(components: [
                Group::make()
                    ->columns()
                    ->columnSpanFull()
                    ->statePath(path: 'preference')
                    ->schema(
                        components: [
                            Section::make(heading:'Ballot Access')
                                ->columnSpan(span: 1)
                                ->description(description: 'Electors will be able to access their eligible ballot by these options')
                                ->schema(components: [
                                    Toggle::make(name: 'ballot_link_common')
                                        ->label(label: 'Common link')
                                        ->rule(
                                            rule: fn (Field $component) => 'accepted_if:'.$component->getContainer()->getStatePath().'.ballot_link_unique,false'
                                        )
                                        ->validationMessages(messages: [
                                            'accepted_if' => 'This must be enabled when unique link is disabled'
                                        ]),

                                    Toggle::make(name: 'ballot_link_unique')
                                        ->label(label: 'Unique link')
                                        ->rule(
                                            rule: fn (Get $get) => function (string $attribute, bool $value, Closure $fail) use ($get): void{
                                                if (
                                                    $value &&
                                                    !$get(path: 'ballot_link_email') &&
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
                                ->schema(components: [
                                    Toggle::make(name: 'ip_restriction')
                                        ->dehydrated()
                                        ->formatStateUsing(callback: static fn (Get $get): bool => $get(path: 'ip_restriction_threshold') ?? false)
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
                                ->description(description: 'Ballot Link for each voters will be sent through this medium')
                                ->schema(components: [
                                    Toggle::make(name: 'ballot_link_mail')
                                        ->label(label: 'Email'),

                                    Toggle::make(name: 'ballot_link_sms')
                                        ->label(label: 'SMS'),

                                    Toggle::make(name: 'ballot_link_whatsapp')
                                        ->disabled()
                                        ->hint(hint: 'Coming soon')
                                        ->label(label: 'Whatsapp'),
                                ]),

                            Section::make(heading: 'MFA Code Delivery')
                                ->columnSpan(span: 1)
                                ->description(description: 'Multi-Factor Authentication code for each voters will be sent through this medium')
                                ->schema(components: [
                                    Toggle::make(name: 'mfa_mail')
                                        ->label(label: 'Email'),

                                    Toggle::make(name: 'mfa_sms')
                                        ->label(label: 'SMS'),

                                    Toggle::make(name: 'mfa_whatsapp')
                                        ->disabled()
                                        ->hint(hint: 'Coming soon')
                                        ->label(label: 'Whatsapp'),
                                ]),

                            Section::make(heading: 'Ballot Acknowledgement')
                                ->description(description: 'Elector will receive acknowledgement message for submitting their votes')
                                ->columnSpan(span: 1)
                                ->schema(components: [
                                    Toggle::make(name: 'voted_confirmation_mail')
                                        ->label(label: 'Email'),

                                    Toggle::make(name: 'voted_confirmation_sms')
                                        ->label(label: 'SMS'),

                                    Toggle::make(name: 'voted_confirmation_whatsapp')
                                        ->disabled()
                                        ->hint(hint: 'Coming soon')
                                        ->label(label: 'Whatsapp'),
                                ]),

                            Section::make(heading: 'Sharing of Electors\'s Ballot Copy')
                                ->columnSpan(span: 1)
                                ->description(description: 'Voted ballot copy will be shared to electors through email and / or they can also download directly after submitting their votes')
                                ->schema(components: [
                                    Toggle::make(name: 'voted_ballot_download')
                                        ->label(label: 'Direct download'),

                                    Toggle::make(name: 'voted_ballot_mail')
                                        ->label(label: 'Email'),

                                    Toggle::make(name: 'voted_ballot_whatsapp')
                                        ->disabled()
                                        ->hint(hint: 'Coming soon')
                                        ->label(label: 'Whatsapp'),
                                ]),

                            Section::make(heading: 'Security preference')
                                ->columns()
                                ->schema(components: [
                                    Toggle::make(name: 'dnt_votes')
                                        ->afterStateUpdated(callback: function (bool $state, Set $set): void {
                                            if (! $state) {
                                                return;
                                            }

                                            $set(path: 'voted_ballot_update', state: false);
                                        })
                                        ->default(state: true)
                                        ->helperText(text: 'This ensures nobody can track which elector voted for whom. However some additional options will be disabled when enabling this option.')
                                        ->label(label: 'Do Not Track electors\'s votes')
                                        ->live(),

                                    Toggle::make(name: 'voted_ballot_update')
                                        ->helperText(text: 'Elector\'s can change their votes even after submitting their votes until election closes')
                                        ->label(label: 'Editable votes')
                                        ->disabled(condition: fn (Get $get): bool => $get(path: 'dnt_votes')),
                                ]),

                            Section::make(heading: 'Candidate preference')
                                ->columns()
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

                                    Toggle::make(name: 'candidate_photo')
                                        ->label(label: 'Allow candidate photo'),

                                    Toggle::make(name: 'candidate_bio')
                                        ->label(label: 'Allow candidate bio text'),

                                    Toggle::make(name: 'candidate_attachment')
                                        ->label(label: 'Allow candidate attachments'),
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
            ->visible(condition: $this->canSave());
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
