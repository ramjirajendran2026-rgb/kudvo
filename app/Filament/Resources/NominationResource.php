<?php

namespace App\Filament\Resources;

use App\Filament\Forms\NominationForm;
use App\Filament\Resources\NominationResource\Pages;
use App\Models\Nomination;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;

class NominationResource extends Resource
{
    protected static ?string $model = Nomination::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(columns: null)
            ->schema(components: [
                NominationForm::nameComponent(),

                NominationForm::descriptionComponent(),

                NominationForm::selfNominationComponent(),

                NominationForm::nominatorThresholdComponent(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(url: fn (Nomination $record) => static::getUrl(name: 'dashboard', parameters: [$record]))
            ->columns([
                Tables\Columns\TextColumn::make(name: 'code')
                    ->badge()
                    ->copyable()
                    ->icon(icon: 'heroicon-m-clipboard-document')
                    ->iconPosition(iconPosition: IconPosition::After)
                    ->label(label: __(key: 'filament/tables/nomination.code.label'))
                    ->searchable(),

                Tables\Columns\TextColumn::make(name: 'name')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\IconColumn::make(name: 'self_nomination')
                    ->boolean(),

                Tables\Columns\TextColumn::make(name: 'nominator_threshold')
                    ->numeric(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNominations::route(path: '/'),
            'dashboard' => Pages\Dashboard::route(path: '{record}'),
            'electors' => Pages\Electors::route(path: '{record}/electors'),
            'positions' => Pages\Positions::route(path: '{record}/positions'),
            'nominees' => Pages\Nominees::route(path: '{record}/nominees'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems(pages: [
            Pages\Dashboard::class,
            Pages\Electors::class,
            Pages\Positions::class,
            Pages\Nominees::class,
        ]);
    }
}
