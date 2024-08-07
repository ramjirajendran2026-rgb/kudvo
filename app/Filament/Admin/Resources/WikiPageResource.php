<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WikiPageResource\Pages;
use App\Models\WikiPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Auth\Access\AuthorizationException;

use function Filament\authorize;

class WikiPageResource extends Resource
{
    protected static ?string $model = WikiPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';

    protected static ?string $navigationGroup = 'Wiki';

    protected static ?string $navigationLabel = 'Pages';

    protected static ?int $navigationSort = 501;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(columns: null)
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\Section::make()
                        ->columnSpan(3)
                        ->schema(components: [
                            Forms\Components\TextInput::make(name: 'title')
                                ->afterStateUpdated(callback: function (?string $state, Forms\Get $get, Forms\Set $set, ?string $old) {
                                    if ($old == $get('seo.title')) {
                                        $set('seo.title', $state);
                                    }
                                })
                                ->live(onBlur: true)
                                ->maxLength(length: 255)
                                ->required(),

                            Forms\Components\SpatieMediaLibraryFileUpload::make(name: 'cover')
                                ->collection(collection: WikiPage::MEDIA_COLLECTION_COVER)
                                ->image()
                                ->imageCropAspectRatio(ratio: '16:9')
                                ->imageEditor()
                                ->imageEditorAspectRatios(ratios: ['16:9', '1:1']),

                            Forms\Components\Textarea::make(name: 'summary')
                                ->afterStateUpdated(callback: function (?string $state, Forms\Get $get, Forms\Set $set, ?string $old) {
                                    if ($old == $get('seo.description')) {
                                        $set('seo.description', $state);
                                    }
                                })
                                ->live(onBlur: true),

                            Forms\Components\RichEditor::make(name: 'content'),
                        ]),

                    Forms\Components\Group::make([
                        Forms\Components\Section::make()
                            ->schema(components: [
                                Forms\Components\Select::make('category')
                                    ->createOptionAction(
                                        fn (Forms\Components\Actions\Action $action) => $action
                                            ->modalHeading('Create category')
                                            ->modalWidth(MaxWidth::Medium)
                                    )
                                    ->createOptionForm(fn (Form $form) => WikiCategoryResource::form($form))
                                    ->preload()
                                    ->relationship(titleAttribute: 'name')
                                    ->required()
                                    ->searchable(),

                                Forms\Components\Select::make('tags')
                                    ->createOptionAction(
                                        fn (Forms\Components\Actions\Action $action) => $action
                                            ->modalHeading('Create tag')
                                            ->modalWidth(MaxWidth::Medium)
                                    )
                                    ->createOptionForm(fn (Form $form) => WikiTagResource::form($form))
                                    ->multiple()
                                    ->preload()
                                    ->relationship(titleAttribute: 'name')
                                    ->searchable(),
                            ]),

                        Forms\Components\Section::make(heading: 'SEO')
                            ->relationship(name: 'seo')
                            ->schema(components: [
                                Forms\Components\TextInput::make(name: 'title')
                                    ->charCounter()
                                    ->maxLength(length: 255),

                                Forms\Components\Textarea::make(name: 'description')
                                    ->charCounter(count: 160)
                                    ->maxLength(length: 255),
                            ]),
                    ]),
                ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'sm' => 2
            ])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make(name: 'category.name')
                            ->badge()
                            ->color('primary')
                            ->grow(false)
                            ->wrap(),

                        Tables\Columns\TextColumn::make(name: 'tags.name')
                            ->badge()
                            ->color('info')
                            ->wrap(),
                    ]),

                    Tables\Columns\TextColumn::make(name: 'title')
                        ->wrap(),
                ])->space(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
                Tables\Actions\RestoreAction::make()
                    ->iconButton(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->preload()
                    ->relationship(name: 'category', titleAttribute: 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('tags')
                    ->preload()
                    ->relationship(name: 'tags', titleAttribute: 'name')
                    ->searchable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWikiPages::route('/'),
            'create' => Pages\CreateWikiPage::route('/create'),
            'edit' => Pages\EditWikiPage::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        if (static::shouldSkipAuthorization()) {
            return true;
        }

        $model = static::getModel();

        try {
            return authorize('viewAny', $record ?? $model, static::shouldCheckPolicyExistence())->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }
}
