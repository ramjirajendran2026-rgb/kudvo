<?php

namespace App\Filament\Admin\Clusters\Stripe\Resources;

use App\Enums\StripeCouponDuration;
use App\Filament\Admin\Clusters\Stripe;
use App\Filament\Admin\Clusters\Stripe\Resources\StripeCouponResource\Pages;
use App\Forms\Components\CurrencyPicker;
use App\Models\StripeCoupon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StripeCouponResource extends Resource
{
    protected static ?string $model = StripeCoupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Stripe::class;

    protected static ?string $modelLabel = 'Coupon';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->maxLength(100)
                    ->required(),

                TextInput::make('percent_off')
                    ->disabledOn('edit')
                    ->numeric()
                    ->minValue(0.01)
                    ->maxValue(100)
                    ->requiredWithout('amount_off'),

                TextInput::make('amount_off')
                    ->disabledOn('edit')
                    ->integer()
                    ->minValue(1)
                    ->requiredWithout('percent_off'),

                CurrencyPicker::make()
                    ->disabledOn('edit')
                    ->requiredWith('amount_off'),

                Select::make('duration')
                    ->disabledOn('edit')
                    ->enum(StripeCouponDuration::class)
                    ->options(StripeCouponDuration::getOptions())
                    ->default(StripeCouponDuration::Once)
                    ->required(),

                TextInput::make('max_redemptions')
                    ->disabledOn('edit')
                    ->integer()
                    ->minValue(1),

                DateTimePicker::make('redeem_by')
                    ->disabledOn('edit'),

                KeyValue::make('metadata'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStripeCoupons::route('/'),
            'create' => Pages\CreateStripeCoupon::route('/create'),
            'edit' => Pages\EditStripeCoupon::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name')->wrap(),
                Tables\Columns\TextColumn::make('percent_off')->suffix('%')->fontFamily('mono'),
                Tables\Columns\TextColumn::make('amount_off')->money(fn (StripeCoupon $record) => $record->currency)->fontFamily('mono'),
                Tables\Columns\TextColumn::make('duration')->badge(),
                Tables\Columns\TextColumn::make('max_redemptions'),
                Tables\Columns\TextColumn::make('redeem_by')->dateTime(),
                Tables\Columns\IconColumn::make('livemode')->boolean(),
                Tables\Columns\IconColumn::make('valid')->boolean(),
                Tables\Columns\TextColumn::make('created')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
