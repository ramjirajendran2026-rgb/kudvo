<?php

namespace App\Filament\Admin\Clusters\SmsSettingsCluster\Resources;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use App\Enums\SmsChannel;
use App\Filament\Admin\Clusters\SmsSettingsCluster;
use App\Filament\Admin\Clusters\SmsSettingsCluster\Resources\SmsPriceResource\Pages;
use App\Forms\Components\CountryPicker;
use App\Forms\Components\CurrencyPicker;
use App\Models\SmsPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Nnjeim\World\Models\Country;

class SmsPriceResource extends Resource
{
    protected static ?string $model = SmsPrice::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $activeNavigationIcon = 'heroicon-s-currency-dollar';

    protected static ?string $modelLabel = 'Price';

    protected static ?string $cluster = SmsSettingsCluster::class;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(null)
            ->schema([
                Forms\Components\Select::make('channel')
                    ->enum(SmsChannel::class)
                    ->options(Arr::mapWithKeys(SmsChannel::cases(), fn (SmsChannel $enum) => [$enum->value => Str::title($enum->value)]))
                    ->required(),
                CountryPicker::make()
                    ->required(),
                CurrencyPicker::make()
                    ->dehydrateStateUsing(fn (string $state) => Str::upper($state))
                    ->required(),
                Forms\Components\TextInput::make('actual_price')
                    ->helperText(text: 'Must be in lowest currency unit (e.g. cents or paise)')
                    ->minValue(value: 0)
                    ->numeric()
                    ->step(interval: 0)
                    ->required(),
                Forms\Components\TextInput::make('margin')
                    ->helperText(text: 'Must be in lowest currency unit (e.g. cents or paise)')
                    ->minValue(value: 0)
                    ->numeric()
                    ->step(interval: 0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('channel'),
                Tables\Columns\TextColumn::make('country')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->alignEnd()
                    ->fontFamily('mono')
                    ->formatStateUsing(fn (Money $state) => $state->format())
                    ->getStateUsing(fn (SmsPrice $record) => new Money($record->price, new Currency($record->currency)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->alignCenter()
                    ->badge(),
                Tables\Columns\TextColumn::make('actual_price')
                    ->alignEnd()
                    ->fontFamily('mono')
                    ->formatStateUsing(fn (Money $state) => $state->format())
                    ->getStateUsing(fn (SmsPrice $record) => new Money($record->actual_price, new Currency($record->currency)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('margin')
                    ->alignEnd()
                    ->fontFamily('mono')
                    ->formatStateUsing(fn (Money $state) => $state->format())
                    ->getStateUsing(fn (SmsPrice $record) => new Money($record->margin, new Currency($record->currency)))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->options(Arr::mapWithKeys(SmsChannel::cases(), fn (SmsChannel $enum) => [$enum->value => Str::title($enum->value)])),

                Tables\Filters\SelectFilter::make('country')
                    ->options(Country::all()->pluck('name', 'iso2')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ManageSmsPrices::route('/'),
        ];
    }
}
