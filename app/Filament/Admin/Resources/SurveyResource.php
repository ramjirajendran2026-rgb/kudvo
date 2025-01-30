<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SurveyResource\Pages;
use App\Models\Survey;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(null)
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->hiddenLabel()
                            ->placeholder('Survey name')
                            ->required(),

                        TiptapEditor::make('settings.description')
                            ->hiddenLabel()
                            ->maxContentWidth(MaxWidth::Full->value)
                            ->placeholder('Survey description'),

                        Toggle::make('settings.accept-guest-entries')
                            ->default(true),
                    ]),

                Repeater::make('questions')
                    ->reorderable('order')
                    ->relationship()
                    ->reorderable()
                    ->orderColumn()
                    ->schema([
                        Group::make([
                            TextInput::make('content')
                                ->columnSpan(4)
                                ->hiddenLabel()
                                ->placeholder('Question content')
                                ->required(),

                            Select::make('type')
                                ->default('text')
                                ->hiddenLabel()
                                ->options([
                                    'text' => 'Text',
                                    'number' => 'Number',
                                    'radio' => 'Single-selection',
                                    'multiselect' => 'Multi-selection',
                                ])
                                ->placeholder('Select type')
                                ->required(),
                        ])->columns(5),

                        TagsInput::make('options')
                            ->hiddenLabel()
                            ->placeholder('New option'),

                        TagsInput::make('rules')
                            ->suggestions([
                                'required',
                            ])
                            ->hiddenLabel()
                            ->placeholder('New rule'),
                    ]),

                SpatieMediaLibraryFileUpload::make('footer_images')
                    ->appendFiles()
                    ->collection(Survey::MEDIA_COLLECTION_FOOTER_IMAGES)
                    ->image()
                    ->imagePreviewHeight(100)
                    ->imageResizeMode('cover')
                    ->imageResizeTargetHeight(100)
                    ->maxFiles(15)
                    ->multiple()
                    ->panelLayout('grid')
                    ->reorderable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),

                Tables\Columns\TextColumn::make('entries_count')
                    ->alignCenter()
                    ->counts('entries'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }
}
