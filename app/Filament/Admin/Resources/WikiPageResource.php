<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WikiPageResource\Pages;
use App\Filament\Admin\Resources\WikiPageResource\RelationManagers;
use App\Models\WikiPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\TextInput::make(name: 'title')
                    ->maxLength(length: 255)
                    ->required(),

                Forms\Components\SpatieMediaLibraryFileUpload::make(name: 'cover')
                    ->collection(collection: WikiPage::MEDIA_COLLECTION_COVER)
                    ->image()
                    ->imageCropAspectRatio(ratio: '16:9')
                    ->imageEditor()
                    ->imageEditorAspectRatios(ratios: ['16:9', '1:1']),

                Forms\Components\Textarea::make(name: 'summary'),

                Forms\Components\RichEditor::make(name: 'content'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWikiPages::route('/'),
            'create' => Pages\CreateWikiPage::route('/create'),
            'edit' => Pages\EditWikiPage::route('/{record}/edit'),
        ];
    }
}
