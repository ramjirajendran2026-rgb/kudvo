<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Data\Election\PreferenceData;
use App\Enums\CandidateSort;
use App\Models\Election;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LaraZeus\Quantity\Components\Quantity;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\Mime\MimeTypes;

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
                                ->description(description: 'Electors will receive their ballot link through these medium.')
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
                                ->description(description: 'Electors will receive MFA code through these medium. This will be used to verify the elector\'s identity before submitting their votes.')
                                ->schema(components: [
                                    Toggle::make(name: 'mfa_mail')
                                        ->label(label: 'Email')
                                        ->live(),

                                    Split::make(schema: [
                                        Toggle::make(name: 'mfa_sms')
                                            ->grow(condition: false)
                                            ->label(label: 'SMS')
                                            ->live(),

                                        Toggle::make(name: 'mfa_sms_auto_fill_only')
                                            ->hintIcon(icon: 'heroicon-o-information-circle')
                                            ->hintIconTooltip(tooltip: 'Supports only on Android (Chrome) and iOS (Safari) devices. Voting from other devices will be restricted.')
                                            ->label(label: 'Prevent manual entry')
                                            ->visible(condition: static fn (Get $get): bool => $get(path: 'mfa_sms') && !$get(path: 'mfa_mail')),
                                    ])->label(label: 'SMS'),

                                    Toggle::make(name: 'mfa_whatsapp')
                                        ->disabled()
                                        ->hint(hint: 'Coming soon')
                                        ->label(label: 'Whatsapp'),
                                ]),

                            Section::make(heading: 'Ballot Acknowledgement')
                                ->description(description: 'Electors will receive confirmation of their votes through these medium.')
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
                                ->description(description: 'Electors will be able to share their voted ballot copy through these medium.')
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
                                    Group::make()
                                        ->columns()
                                        ->columnSpanFull()
                                        ->schema(components: [
                                            Toggle::make(name: 'dnt_votes')
                                                ->afterStateUpdated(callback: function (bool $state, Set $set): void {
                                                    if (! $state) {
                                                        return;
                                                    }

                                                    $set(path: 'voted_ballot_update', state: false);
                                                })
                                                ->default(state: true)
                                                ->helperText(text: 'This will prevent the system from tracking the electors\' votes.')
                                                ->label(label: 'Do Not Track electors\'s votes')
                                                ->live(),

                                            Toggle::make(name: 'voted_ballot_update')
                                                ->helperText(text: 'This will allow electors to update their voted ballot.')
                                                ->label(label: 'Editable votes')
                                                ->disabled(condition: fn (Get $get): bool => $get(path: 'dnt_votes')),
                                        ]),

                                    Toggle::make(name: 'prevent_duplicate_device')
                                        ->label(label: 'Prevent duplicate device')
                                        ->helperText(text: 'This will prevent electors from casting votes from same device.')
                                        ->hint(hint: 'Experimental')
                                        ->hintIcon(
                                            icon: 'heroicon-o-information-circle',
                                            tooltip: 'This is experimental and may not work as expected. Please use with caution.'
                                        ),
                                ]),

                            Section::make(heading: 'Elector preference')
                                ->columns()
                                ->schema(components: [
                                    Toggle::make(name: 'elector_duplicate_email')
                                        ->helperText(text: 'This will allow you to add multiple electors with same email address.')
                                        ->label(label: 'Duplicate email addresses'),

                                    Toggle::make(name: 'elector_duplicate_phone')
                                        ->helperText(text: 'This will allow you to add multiple electors with same phone number.')
                                        ->label(label: 'Duplicate phone numbers'),

                                    Toggle::make(name: 'elector_update_after_publish')
                                        ->helperText(text: 'This will allow you to update the elector details after the election is published.')
                                        ->label(label: 'Allow elector update after publish'),
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
                                        ->label(label: 'Enable candidate photo'),

                                    Toggle::make(name: 'candidate_symbol')
                                        ->label(label: 'Enable candidate symbol'),

                                    Toggle::make(name: 'candidate_bio')
                                        ->label(label: 'Enable candidate bio text'),

                                    Toggle::make(name: 'candidate_attachment')
                                        ->label(label: 'Enable candidate attachments'),
                                ]),

                            Section::make(heading: 'Booth voting')
                                ->description(description: 'This will allow electors to cast votes from a specific location. This is useful for conducting elections in a physical location.')
                                ->schema(components: [
                                    Toggle::make(name: 'booth_voting')
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
