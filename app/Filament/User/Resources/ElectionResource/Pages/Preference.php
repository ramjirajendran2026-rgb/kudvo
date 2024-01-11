<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\CandidateSort;
use App\Filament\User\Resources\ElectionResource;
use App\Models\Election;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconSize;
use Illuminate\Support\Arr;

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

        $this->form->fill($this->getElection()->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(condition: !$this->canSave())
            ->schema(components: [
                Group::make()
                    ->columns()
                    ->columnSpanFull()
                    ->relationship(name: 'preference')
                    ->schema(
                        components: [
                            Section::make('EUL Delivery')
                                ->description('Encrypted Unique Link for each voters will be sent through this medium')
                                ->schema([
                                    Toggle::make('eul_mail')
                                        ->label('Email')
                                        ->default(true),

                                    Toggle::make('eul_sms')
                                        ->label('SMS'),
                                ])
                                ->columns(3)
                                ->columnSpan(1),

                            Section::make('MFA Code Delivery')
                                ->description('Multi-Factor Authentication code for each voters will be sent through this medium')
                                ->columns(3)
                                ->columnSpan(1)
                                ->schema([
                                    Toggle::make('mfa_mail')
                                        ->label('Email')
                                        ->default(true),

                                    Toggle::make('mfa_sms')
                                        ->label('SMS'),
                                ]),

                            Section::make('Ballot Acknowledgement')
                                ->description('Elector will receive acknowledgement message for submitting their votes')
                                ->columns(3)
                                ->columnSpan(1)
                                ->schema([
                                    Toggle::make('voted_confirmation_mail')
                                        ->label('Email')
                                        ->default(true),

                                    Toggle::make('voted_confirmation_sms')
                                        ->label('SMS'),
                                ]),

                            Section::make('IP Restriction')
                                ->description('Restrict electors voting from same IP address')
                                ->columnSpan(1)
                                ->schema([
                                    Toggle::make('ip_restriction')
                                        ->dehydrated()
                                        ->label('Enable')
                                        ->live(),

                                    TextInput::make('ip_restriction_threshold')
                                        ->extraInputAttributes(['class' => 'text-center text-xl font-bold'])
                                        ->formatStateUsing(static fn (?int $state) => $state ?: 1)
                                        ->hiddenLabel()
                                        ->prefix('Max. votes')
                                        ->maxValue(10000)
                                        ->minValue(1)
                                        ->numeric()
                                        ->required()
                                        ->suffixActions(
                                            actions: [\Filament\Forms\Components\Actions\Action::make('decrement')
                                                ->action(fn (TextInput $component, ?int $state) => $component->state((filled($state) ? $state : 2) - 1))
                                                ->icon('heroicon-m-minus')
                                                ->iconSize(IconSize::Small)
                                                ->hidden(fn (?int $state) => $state <= 1),

                                                \Filament\Forms\Components\Actions\Action::make('increment')
                                                    ->action(fn (TextInput $component, ?int $state) => $component->state((filled($state) ? $state : 0) + 1))
                                                    ->icon('heroicon-m-plus')
                                                    ->iconSize(IconSize::Small),
                                            ],
                                        )
                                        ->visible(static fn (Get $get) => $get('ip_restriction')),
                                ]),

                            Section::make('Sharing of Electors\'s Ballot Copy')
                                ->description('Voted ballot copy will be shared to electors through email and / or they can also download directly after submitting their votes')
                                ->columns(3)
                                ->schema([
                                    Toggle::make('voted_ballot_mail')
                                        ->label('Email'),

                                    Toggle::make('voted_ballot_download')
                                        ->label('Direct download'),
                                ]),

                            Section::make('Security preference')
                                ->columns()
                                ->schema([
                                    Toggle::make('dnt_votes')
                                        ->afterStateUpdated(function (bool $state, Set $set) {
                                            if (! $state) {
                                                return;
                                            }

                                            $set('voted_ballot_update', false);
                                        })
                                        ->default(true)
                                        ->helperText('This ensures nobody can track which elector voted for whom. However some additional options will be disabled when enabling this option.')
                                        ->label('Do Not Track electors\'s votes')
                                        ->live(),

                                    Toggle::make('voted_ballot_update')
                                        ->helperText('Elector\'s can change their votes even after submitting their votes until election closes')
                                        ->label('Editable votes')
                                        ->disabled(fn (Get $get) => $get('dnt_votes')),
                                ]),

                            Section::make('Candidate preference')
                                ->columns()
                                ->schema([
                                    Select::make('candidate_sort')
                                        ->label('Display order')
                                        ->columnSpanFull()
                                        ->default(CandidateSort::MANUAL->value)
                                        ->enum(CandidateSort::class)
                                        ->columns(6)
                                        ->native(false)
                                        ->options(Arr::mapWithKeys(
                                            CandidateSort::cases(),
                                            fn (CandidateSort $case) => [$case->value => $case->getLabel()]
                                        )),

                                    Toggle::make('candidate_photo')
                                        ->label('Allow candidate photo'),

                                    Toggle::make('candidate_bio')
                                        ->label('Allow candidate bio text'),

                                    Toggle::make('candidate_attachment')
                                        ->label('Allow candidate attachments'),
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

        $this->form->getState();

        Notification::make()
            ->success()
            ->title(title: 'Saved')
            ->send();

        $this->redirect(url: Dashboard::getUrl(parameters: [$this->getElection()]));
    }
}
