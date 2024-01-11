<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\NominationResource\Pages\Dashboard;
use App\Filament\User\Resources\NominationResource\Pages\Electors;
use App\Filament\User\Resources\NominationResource\Pages\ManageNominations;
use App\Filament\User\Resources\NominationResource\Pages\Nominees;
use App\Filament\User\Resources\NominationResource\Pages\Positions;
use App\Filament\User\Resources\NominationResource\Pages\Preference;
use App\Filament\User\Resources\NominationResource\Widgets\NominationStatsOverview;
use App\Forms\NominationForm;
use App\Models\Nomination;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

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

                NominationForm::nominatorThresholdComponent()
                    ->inlineLabel(),
            ]);
    }

    public static function timingForm(Form $form): Form
    {
        return $form
            ->schema(components: [
                NominationForm::timezoneComponent(),

                NominationForm::startsAtComponent()
                    ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),

                NominationForm::endsAtComponent()
                    ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateActions(actions: [
                static::getCreateAction(),
            ])
            ->emptyStateIcon(icon: static::getNavigationIcon())
            ->headerActions(actions: [
                static::getCreateAction(),
            ])
            ->heading(heading: Str::title(value: static::getPluralModelLabel()))
            ->recordUrl(url: fn (Nomination $record) => static::getUrl(name: 'dashboard', parameters: [$record]))
            ->relationship(relationship: fn (): Relation => Filament::getTenant()?->nominations())
            ->columns([
                Tables\Columns\TextColumn::make(name: 'code')
                    ->badge()
                    ->copyable()
                    ->icon(icon: 'heroicon-m-clipboard-document')
                    ->iconPosition(iconPosition: IconPosition::After)
                    ->label(label: 'Code')
                    ->searchable(),

                Tables\Columns\TextColumn::make(name: 'name')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make(name: 'status')
                    ->alignCenter()
                    ->badge(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNominations::route(path: '/'),
            'dashboard' => Dashboard::route(path: '{record}'),
            'preference' => Preference::route(path: '{record}/preference'),
            'electors' => Electors::route(path: '{record}/electors'),
            'positions' => Positions::route(path: '{record}/positions'),
            'nominees' => Nominees::route(path: '{record}/nominees'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems(components: [
            Dashboard::class,
            Preference::class,
            Electors::class,
            Positions::class,
            Nominees::class,
        ]);
    }

    public static function getWidgets(): array
    {
        return [
            NominationStatsOverview::class,
        ];
    }

    public static function getCreateAction(): Tables\Actions\CreateAction
    {
        return Tables\Actions\CreateAction::make()
            ->createAnother(condition: false)
            ->modalFooterActionsAlignment(alignment: Alignment::End)
            ->successRedirectUrl(url: fn (Nomination $record) => static::getUrl(name: 'dashboard', parameters: [$record]));
    }
}
