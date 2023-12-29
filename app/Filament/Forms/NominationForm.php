<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;

readonly class NominationForm
{
    public static function nameComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->label(label: __(key: 'filament/forms/nomination.name.label'))
            ->maxLength(length: 250)
            ->minLength(length: 5)
            ->required();
    }

    public static function descriptionComponent(): RichEditor
    {
        return RichEditor::make(name: 'description')
            ->label(label: __(key: 'filament/forms/nomination.description.label'))
            ->maxLength(length: 2500);
    }

    public static function selfNominationComponent(): Toggle
    {
        return Toggle::make(name: 'self_nomination')
            ->afterStateUpdated(callback: function (Get $get, Set $set, bool $state): void {
                if (! $state && ! $get(path: 'nominator_threshold')) {
                    $set(path: 'nominator_threshold', state: 1);
                }
            })
            ->label(label: __(key: 'filament/forms/nomination.self_nomination.label'))
            ->live();
    }

    public static function nominatorThresholdComponent(): TextInput
    {
        return TextInput::make(name: 'nominator_threshold')
            ->default(state: 2)
            ->helperText(text: __(key: 'filament/forms/nomination.nominator_threshold.helper_text'))
            ->label(label: __(key: 'filament/forms/nomination.nominator_threshold.label'))
            ->maxValue(value: 20)
            ->minValue(value: fn (Get $get) => $get(path: 'self_nomination') ? 0 : 1)
            ->numeric()
            ->required();
    }
}
