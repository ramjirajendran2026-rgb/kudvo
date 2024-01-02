<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;

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
        return TextInput::make(name: 'quota')
            ->default(state: 1)
            ->label(label: 'Available posts')
            ->maxValue(value: 25)
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
        return TextInput::make(name: 'threshold')
            ->default(state: 0)
            ->label(label: 'Min selection')
            ->minValue(value: 0)
            ->numeric()
            ->required()
            ->rule(rule: fn (Get $get): string => 'max:'.$get(path: 'quota') ?? 0)
            ->visible(condition: fn (Get $get): bool => $get(path: 'abstain') ?? false);
    }
}
