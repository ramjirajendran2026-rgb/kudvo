<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WikiPageResource\Pages;
use App\Models\WikiPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Auth\Access\AuthorizationException;

use function Filament\authorize;

class WikiPageResource extends Resource
{
    protected static ?string $model = WikiPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'wiki';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(columns: null)
            ->schema([
                Forms\Components\Section::make()
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(name: 'title')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
                Tables\Actions\RestoreAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
