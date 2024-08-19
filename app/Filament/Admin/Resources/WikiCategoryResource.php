<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WikiCategoryResource\Pages;
use App\Models\WikiCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class WikiCategoryResource extends Resource
{
    protected static ?string $model = WikiCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $activeNavigationIcon = 'heroicon-s-folder';

    protected static ?string $navigationGroup = 'Wiki';

    protected static ?string $navigationLabel = 'Categories';

    protected static ?int $navigationSort = 502;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(null)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->afterStateUpdated(function (?string $state, ?string $old, Forms\Get $get, Forms\Set $set) {
                        $hasTitleSlug = $get('slug') == Str::slug($old ?? '');

                        if (! $hasTitleSlug) {
                            return;
                        }

                        $set('slug', Str::slug($state ?? ''));
                    })
                    ->afterStateUpdated(function (?string $state, ?string $old, Forms\Get $get, Forms\Set $set) {
                        $hasSameTitle = $get('seo.title') == $old;

                        if (! $hasSameTitle) {
                            return;
                        }

                        $set('seo.title', $state);
                    })
                    ->live(onBlur: true)
                    ->maxLength(80)
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('slug')
                    ->hint(function (Forms\Components\TextInput $component) {
                        $statePath = $component->generateRelativeStatePath('name');

                        return new HtmlString(
                            <<<HTML
<span wire:loading wire:target="$statePath">Generating...</span>
HTML
                        );
                    })
                    ->maxLength(80)
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\Section::make(heading: 'SEO')
                    ->columns(null)
                    ->compact()
                    ->relationship(name: 'seo')
                    ->schema(components: [
                        Forms\Components\TextInput::make(name: 'title')
                            ->charCounter(60)
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
                Tables\Columns\TextColumn::make(name: 'name')
                    ->wrap(),

                Tables\Columns\TextColumn::make(name: 'slug')
                    ->wrap(),

                Tables\Columns\TextColumn::make('pages_count')
                    ->alignCenter()
                    ->color('info')
                    ->counts('pages')
                    ->label('Pages')
                    ->url(fn (WikiCategory $record) => WikiPageResource::getUrl(parameters: ['tableFilters' => ['category' => ['value' => $record->getKey()]]])),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->modalWidth(MaxWidth::Medium),

                Tables\Actions\DeleteAction::make()
                    ->iconButton(),

                Tables\Actions\RestoreAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWikiCategories::route('/'),
        ];
    }
}
