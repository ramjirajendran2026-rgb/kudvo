<?php

namespace App\Filament\Admin\Clusters\ElectionManagement\Resources;

use App\Filament\Admin\Clusters\ElectionManagement;
use App\Filament\Admin\Clusters\ElectionManagement\Resources\ElectionPriceResource\Pages;
use App\Filament\Admin\Clusters\ElectionManagement\Resources\ElectionPriceResource\RelationManagers;
use App\Forms\Components\MoneyInput;
use App\Models\ElectionPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ElectionPriceResource extends Resource
{
    protected static ?string $model = ElectionPrice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = ElectionManagement::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make(name: 'currency')
                    ->inlineLabel()
                    ->live(onBlur: true)
                    ->required(),

                MoneyInput::make(name: 'base_fee'),

                Forms\Components\Toggle::make(name: 'enabled')
                    ->default(state: true),

                Forms\Components\Fieldset::make(label: 'Elector Fee')
                    ->statePath(path: 'elector_fee_breakup')
                    ->schema(components: [
                        MoneyInput::make(name: 'base_fee'),

                        MoneyInput::make(name: 'ballot_link_common'),

                        MoneyInput::make(name: 'ballot_link_unique'),

                        MoneyInput::make(name: 'ballot_link_mail'),

                        MoneyInput::make(name: 'ballot_link_sms'),

                        MoneyInput::make(name: 'mfa_mail'),

                        MoneyInput::make(name: 'mfa_sms'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(name: 'currency')
                    ->badge(),

                Tables\Columns\TextColumn::make(name: 'base_fee')
                    ->money(currency: fn (ElectionPrice $record): ?string => $record->currency, divideBy: 100),

                Tables\Columns\TextColumn::make(name: 'elector_fee')
                    ->getStateUsing(callback: fn (ElectionPrice $record): ?int => array_sum(array: $record->elector_fee_breakup->toArray()))
                    ->money(currency: fn (ElectionPrice $record): ?string => $record->currency, divideBy: 100),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageElectionPrices::route('/'),
        ];
    }
}
