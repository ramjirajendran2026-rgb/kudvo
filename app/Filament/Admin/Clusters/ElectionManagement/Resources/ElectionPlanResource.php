<?php

namespace App\Filament\Admin\Clusters\ElectionManagement\Resources;

use App\Data\Election\PlanFeatureData;
use App\Enums\ElectionFeature;
use App\Filament\Admin\Clusters\ElectionManagement;
use App\Filament\Admin\Clusters\ElectionManagement\Resources\ElectionPlanResource\Pages;
use App\Forms\Components\CurrencyPicker;
use App\Models\ElectionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Squire\Models\Currency;

class ElectionPlanResource extends Resource
{
    protected static ?string $model = ElectionPlan::class;

    protected static ?string $activeNavigationIcon = 'heroicon-s-banknotes';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $cluster = ElectionManagement::class;

    protected static ?string $modelLabel = 'Plan';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(columns: null)
            ->schema(components: []);
    }

    public static function getWizardSteps(): array
    {
        return [
            Forms\Components\Wizard\Step::make(label: 'Basic Details')
                ->columns()
                ->schema(components: [
                    Forms\Components\TextInput::make(name: 'name')
                        ->maxLength(length: 50)
                        ->required(),

                    CurrencyPicker::make()
                        ->required(),

                    Forms\Components\TextInput::make(name: 'base_fee')
                        ->helperText(text: 'Must be in lowest currency unit (e.g. cents or paise)')
                        ->minValue(value: 0)
                        ->numeric()
                        ->step(interval: 0)
                        ->required(),

                    Forms\Components\TextInput::make(name: 'elector_fee')
                        ->helperText(text: 'Must be in lowest currency unit (e.g. cents or paise)')
                        ->minValue(value: 0)
                        ->numeric()
                        ->step(interval: 0)
                        ->required(),

                    Forms\Components\Textarea::make(name: 'description'),
                ]),

            Forms\Components\Wizard\Step::make(label: 'Features')
                ->schema(components: [
                    Forms\Components\Repeater::make(name: 'features')
                        ->addActionLabel(label: 'Add another')
                        ->columns()
                        ->default(state: collect(ElectionFeature::cases())->map(fn (ElectionFeature $feature) => new PlanFeatureData(feature: $feature))->toArray())
                        ->hiddenLabel()
                        ->maxItems(count: count(value: ElectionFeature::cases()))
                        ->schema(components: [
                            Forms\Components\Split::make(schema: [
                                Forms\Components\Select::make(name: 'feature')
                                    ->columnSpanFull()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->enum(enum: ElectionFeature::class)
                                    ->fixIndistinctState()
                                    ->hiddenLabel()
                                    ->options(options: ElectionFeature::class)
                                    ->optionsLimit(limit: count(value: ElectionFeature::cases()))
                                    ->placeholder(placeholder: 'Select a feature')
                                    ->required()
                                    ->searchable(),

                                Forms\Components\Toggle::make(name: 'is_add_on')
                                    ->columnSpanFull()
                                    ->grow(condition: false)
                                    ->live(),

                                Forms\Components\Toggle::make(name: 'show_in_pricing')
                                    ->columnSpanFull()
                                    ->grow(condition: false)
                                    ->live(),
                            ])->columnSpanFull()->from(breakpoint: 'md'),

                            Forms\Components\TextInput::make(name: 'feature_fee')
                                ->helperText(text: 'Must be in lowest currency unit (e.g. cents or paise)')
                                ->minValue(value: 0)
                                ->numeric()
                                ->step(interval: 0)
                                ->visible(condition: fn (Forms\Get $get): bool => $get('is_add_on') ?? false)
                                ->required(),

                            Forms\Components\TextInput::make(name: 'elector_fee')
                                ->helperText(text: 'Must be in lowest currency unit (e.g. cents or paise)')
                                ->minValue(value: 0)
                                ->numeric()
                                ->step(interval: 0)
                                ->visible(condition: fn (Forms\Get $get): bool => $get('is_add_on') ?? false)
                                ->required(),
                        ]),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(name: 'name'),

                Tables\Columns\TextColumn::make(name: 'currency')
                    ->badge()
                    ->formatStateUsing(callback: fn (string $state): string => Str::upper(value: $state)),
            ])
            ->actions(actions: [
                Tables\Actions\ReplicateAction::make()
                    ->form(form: [
                        Forms\Components\TextInput::make(name: 'name')
                            ->maxLength(length: 50)
                            ->required(),

                        CurrencyPicker::make()
                            ->required(),
                    ]),
            ])
            ->filters(filters: [
                Tables\Filters\SelectFilter::make(name: 'currency')
                    ->options(
                        options: Currency::all()
                            ->sortBy(callback: 'name')
                            ->mapWithKeys(callback: fn (Currency $currency) => [$currency->code_alphabetic => Str::upper(value: $currency->code_alphabetic)])
                            ->toArray()
                    )
                    ->optionsLimit(limit: Currency::count())
                    ->searchable(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->reorderable(column: 'sort')
            ->defaultSort(column: 'sort');
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
            'index' => Pages\ListElectionPlans::route('/'),
            'create' => Pages\CreateElectionPlan::route('/create'),
            'edit' => Pages\EditElectionPlan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
