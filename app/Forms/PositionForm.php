<?php

namespace App\Forms;

use App\Filament\Base\Contracts\HasElection;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use LaraZeus\Quantity\Components\Quantity;

readonly class PositionForm
{
    public static function groupsComponent(): Select
    {
        return Select::make(name: 'elector_groups')
            ->label(label:'Eligible groups')
            ->multiple();
    }

    public static function nameComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->label(label: 'Position name')
            ->maxLength(length: 100)
            ->placeholder(placeholder: 'President / Secretary / EC Members etc.,')
            ->required();
    }

    public static function quotaComponent(): TextInput
    {
        return Quantity::make(name: 'quota')
            ->default(state: 1)
            ->label(label: 'Available posts')
            ->maxValue(value: 500)
            ->minValue(value: 1)
            ->numeric()
            ->required();
    }

    public static function abstainComponent(): Toggle
    {
        return Toggle::make(name: 'abstain')
            ->label(label: 'Enable abstain');
    }

    public static function thresholdComponent(): TextInput
    {
        return Quantity::make(name: 'threshold')
            ->default(state: 0)
            ->label(label: 'Min selection')
            ->minValue(value: 0)
            ->numeric()
            ->required()
            ->rule(rule: fn (Get $get): string => 'max:'.(($get(path: 'quota') ?? 1) - 1))
            ->visible(condition: fn (Get $get): bool => $get(path: 'abstain') ?? false);
    }

    public static function segmentsComponent()
    {
        return Select::make(name: 'segments')
            ->relationship(name: 'segments', titleAttribute: 'name')
            ->createOptionAction(
                callback: fn (Action $action) => $action
                    ->modalCancelAction(action: false)
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalHeading(heading: 'Create Segment')
                    ->modalWidth(width: MaxWidth::Large)
            )
            ->createOptionUsing(callback: function (array $data, HasElection $livewire) {
                return $livewire->getElection()->segments()->createOrFirst($data)->getKey();
            })
            ->editOptionAction(
                callback: fn (Action $action) => $action
                    ->modalCancelAction(action: false)
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalWidth(width: MaxWidth::Large)
            )
            ->manageOptionForm(schema: fn (Form $form) => $form->schema([
                TextInput::make(name: 'name')
                    ->label(label: 'Segment name')
                    ->maxLength(length: 150)
                    ->required(),
            ]))
            ->multiple()
            ->preload();
    }
}
