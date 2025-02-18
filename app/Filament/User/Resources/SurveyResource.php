<?php

namespace App\Filament\User\Resources;

use App\Actions\Survey\PublishSurvey;
use App\Enums\SurveyQuestionType;
use App\Filament\User\Resources\SurveyResource\Pages;
use App\Models\Survey;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(null)
            ->schema([
                static::getHasDescriptionComponent(),

                static::getTitleSectionComponent(),

                static::getToggleDescriptionActionsComponent(),

                static::getDescriptionComponent(),

                static::getQuestionsComponent(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->actions([
                EditAction::make()
                    ->iconButton(),
                DeleteAction::make()
                    ->iconButton(),
            ])
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                    Stack::make([
                        TextColumn::make('title')
                            ->description(fn (Survey $record) => str(tiptap_converter()->asText($record->description))->limit())
                            ->searchable()
                            ->size(TextColumn\TextColumnSize::Large)
                            ->weight(FontWeight::SemiBold),

                        TextColumn::make('created_at')
                            ->dateTimeTooltip()
                            ->grow(false)
                            ->since()
                            ->timezone(fn (Survey $record) => Filament::getTenant()?->timezone ?? null),
                    ])
                        ->alignStart()
                        ->space(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'edit' => Pages\EditSurvey::route('/{record}/questions'),
            'responses' => Pages\ManageResponses::route('/{record}/responses'),
        ];
    }

    public static function getTitleComponent(): Textarea
    {
        return Textarea::make('title')
            ->dehydrateStateUsing(fn ($state, Textarea $component) => str_replace(["\r\n", "\n", "\r"], ' ', preg_replace('/\s+/', ' ', $state)))
            ->extraAttributes([
                'class' => '!ring-0 !bg-transparent !shadow-none',
            ])
            ->extraInputAttributes([
                'class' => '!text-2xl font-bold !ps-0',
                'onkeydown' => 'event.key === "Enter" && event.preventDefault()',
            ])
            ->hiddenLabel()
            ->maxLength(500)
            ->placeholder('Survey title')
            ->required()
            ->rows(1);
    }

    public static function getTitleSectionComponent(): Section
    {
        return Section::make()
            ->compact()
            ->extraAttributes([
                'class' => '[&_.fi-section-content]:py-2',
            ])
            ->schema([
                static::getTitleComponent(),
            ]);
    }

    public static function getHasDescriptionComponent(): Hidden
    {
        return Hidden::make('has_description')
            ->afterStateUpdated(fn (bool $state, Set $set) => $set('description', $state ? '' : null))
            ->default(false);
    }

    public static function getToggleDescriptionActionsComponent(): Actions
    {
        return Actions::make([
            Action::make('addDescription')
                ->alpineClickHandler('$wire.set(\'data.has_description\', true)')
                ->tooltip('You can add an image banner, logo, or any video to personalize your survey.')
                ->icon('heroicon-o-information-circle')
                ->iconPosition(IconPosition::After)
                ->link()
                ->visible(condition: fn (Get $get): bool => ! $get('has_description')),
            Action::make('removeDescription')
                ->alpineClickHandler('$wire.set(\'data.has_description\', false)')
                ->link()
                ->visible(condition: fn (Get $get): bool => $get('has_description') ?? false),
        ]);
    }

    public static function getDescriptionComponent(): TiptapEditor
    {
        return TiptapEditor::make('description')
            ->dehydratedWhenHidden()
            ->hiddenLabel()
            ->placeholder('You can add an image banner, logo, or any video to personalize your survey.')
            ->visible(condition: fn (Get $get): bool => $get('has_description') ?? false);
    }

    public static function getQuestionsComponent(): Repeater
    {
        return Repeater::make('questions')
            ->addAction(
                fn (Action $action): Action => $action
                    ->color('primary')
                    ->extraAttributes([
                        'class' => 'w-full !ring-0 border-dashed border-2 border-custom-400 rounded-md p-4 flex items-center justify-center space-x-2',
                    ])
                    ->icon('heroicon-o-plus')
                    ->outlined()
            )
            ->addActionLabel('New question')
            ->defaultItems(1)
            ->minItems(1)
            ->relationship()
            ->orderColumn()
            ->validationMessages([
                'min' => 'You must provide at least :min questions.',
            ])
            ->schema([
                TextInput::make('text')
                    ->hiddenLabel()
                    ->placeholder('Question')
                    ->required(),

                Split::make([
                    Select::make('type')
                        ->default(SurveyQuestionType::ShortAnswer->value)
                        ->enum(SurveyQuestionType::class)
                        ->hiddenLabel()
                        ->live()
                        ->native(false)
                        ->options(SurveyQuestionType::getOptions())
                        ->placeholder('Question type')
                        ->selectablePlaceholder(false),

                    Toggle::make('is_required')
                        ->default(false)
                        ->grow(false)
                        ->label('Required?'),
                ])->from('md'),

                TagsInput::make('options')
                    ->hiddenLabel()
                    ->placeholder('Type option and press enter')
                    ->rules(['min:2', 'max:250'])
                    ->validationMessages(['min' => 'You must provide at least two options.'])
                    ->visible(condition: fn (Get $get): bool => SurveyQuestionType::tryFrom($get('type'))?->canHaveOtherOption() ?? false),

                Toggle::make('has_other_option')
                    ->default(false)
                    ->label('Allow other option?')
                    ->visible(condition: fn (Get $get): bool => SurveyQuestionType::tryFrom($get('type'))?->canHaveOtherOption() ?? false),
            ]);
    }

    public static function getPreviewAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('preview')
            ->authorize('preview')
            ->color('gray')
            ->icon('heroicon-m-eye')
            ->modalCancelAction(false)
            ->modalContent(fn (Survey $record) => new HtmlString(Blade::render(
                <<<'BLADE'
@livewire('survey.entry-form', ['survey' => $survey, 'isPreview' => true])
BLADE
                ,
                ['survey' => $record]
            )))
            ->extraModalWindowAttributes([
                'class' => '[&_.fi-modal-content]:p-0 [&_.fi-modal-content]:bg-gray-50 [&_.fi-modal-content]:dark:bg-gray-950',
            ])
            ->modalSubmitAction(false)
            ->modalWidth(MaxWidth::ScreenMedium)
            ->slideOver();
    }

    public static function getPublishAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('publish')
            ->requiresConfirmation()
            ->authorize('publish')
            ->action(function (Survey $record, \Filament\Actions\Action $action, PublishSurvey $publishSurvey) {
                if (! $publishSurvey->execute($record)) {
                    $action->failure();

                    return;
                }

                $action->success();
            })
            ->color('success')
            ->extraAttributes([
                'wire:dirty.class' => 'hidden',
            ])
            ->icon('heroicon-m-rocket-launch')
            ->modalIcon('heroicon-o-rocket-launch')
            ->successNotificationTitle('Survey published!');
    }

    public static function getSettingsAction()
    {
        return \Filament\Actions\EditAction::make('settings')
            ->icon('heroicon-m-cog-6-tooth')
            ->iconButton()
            ->form([
                Toggle::make('is_active')
                    ->label('Collect responses?'),
            ])
            ->label('Settings')
            ->modalHeading('Settings');
    }

    public static function getShareAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('share')
            ->alpineClickHandler(fn (Survey $record) => sprintf(
                <<<'JS'
navigator.share({
    title: %s,
    text: %s,
    url: %s
})
JS
                ,
                Js::encode($record->title),
                Js::encode('Use this link to participate in the survey'),
                Js::encode(route('survey.entry', ['survey' => $record])),
            ))
            ->icon('heroicon-m-share');
    }

    public static function getCopyLinkAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('copyLink')
            ->alpineClickHandler(fn (Survey $record) => sprintf(
                <<<'JS'
navigator.clipboard.writeText(%s).then(() => new FilamentNotification().title('Copied successfully').success().send())
JS
                ,
                Js::encode(route('survey.entry', ['survey' => $record])),
            ))
            ->icon('heroicon-m-clipboard-document');
    }

    public static function getResponsePageAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('viewResponses')
            ->authorize('viewResponses')
            ->color('success')
            ->icon('heroicon-o-document-text')
            ->label('Responses')
            ->url(fn (Survey $record) => static::getUrl('responses', ['record' => $record]));
    }

    public static function getEditPageAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('editPage')
            ->icon('heroicon-o-document-text')
            ->label('Questions')
            ->url(fn (Survey $record) => static::getUrl('edit', ['record' => $record]));
    }
}
