<?php

namespace App\Filament\Admin\Clusters\Stripe\Resources;

use App\Filament\Admin\Clusters\Stripe;
use App\Filament\Admin\Clusters\Stripe\Resources\StripePromotionCodeResource\Pages;
use App\Forms\Components\CurrencyPicker;
use App\Models\StripeCoupon;
use App\Models\StripePromotionCode;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;

class StripePromotionCodeResource extends Resource
{
    protected static ?string $model = StripePromotionCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Stripe::class;

    protected static ?string $modelLabel = 'Promotion Code';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('coupon_id')
                    ->disabledOn('edit')
                    ->options(StripeCoupon::query()->pluck('name', 'id')->toArray())
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('code')
                    ->disabledOn('edit')
                    ->maxLength(50),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->disabledOn('edit'),
                Forms\Components\Select::make('customer_id')
                    ->disabledOn('edit')
                    ->options(User::query()->whereNotNull('stripe_id')->pluck('email', 'stripe_id')->toArray())
                    ->searchable(),
                Forms\Components\TextInput::make('max_redemptions')
                    ->disabledOn('edit')
                    ->numeric(),
                Forms\Components\Toggle::make('active')
                    ->required(),
                Forms\Components\KeyValue::make('metadata')
                    ->columnSpanFull(),
                Forms\Components\Fieldset::make('Restrictions')
                    ->disabledOn('edit')
                    ->statePath('restrictions')
                    ->schema([
                        Forms\Components\Toggle::make('first_time_transaction')
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\TextInput::make('minimum_amount')
                            ->integer()
                            ->minValue(1),
                        CurrencyPicker::make('minimum_amount_currency'),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStripePromotionCodes::route('/'),
            'create' => Pages\CreateStripePromotionCode::route('/create'),
            'edit' => Pages\EditStripePromotionCode::route('/{record}/edit'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->color('primary')
                    ->copyable()
                    ->fontFamily('mono')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                    ->icon('heroicon-o-clipboard-document')
                    ->iconPosition(IconPosition::After)
                    ->searchable(),
                Tables\Columns\TextColumn::make('coupon.name')
                    ->url(fn (StripePromotionCode $record) => StripeCouponResource\Pages\EditStripeCoupon::getUrl(['record' => $record->coupon_id])),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('customer.email'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime(),
                Tables\Columns\IconColumn::make('livemode')
                    ->boolean(),
                Tables\Columns\TextColumn::make('max_redemptions')
                    ->numeric(),
                Tables\Columns\TextColumn::make('times_redeemed')
                    ->numeric(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
