<?php

namespace App\Filament\Forms;

use App\Forms\Components\TimezonePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;

readonly class NominationForm
{
    public static function descriptionComponent(): RichEditor
    {
        return RichEditor::make(name: 'description')
            ->label(label: 'Description')
            ->maxLength(length: 2500);
    }
    public static function nameComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->label(label: 'Title')
            ->maxLength(length: 250)
            ->minLength(length: 5)
            ->required();
    }

    public static function nominatorThresholdComponent(): TextInput
    {
        return TextInput::make(name: 'nominator_threshold')
            ->default(state: 2)
            ->dehydrateStateUsing(callback: fn (int $state): int => $state + 1)
            ->label(label: 'No. of seconders')
            ->maxValue(value: 20)
            ->minValue(value: 0)
            ->numeric()
            ->required();
    }

    public static function selfNominationComponent(): Toggle
    {
        return Toggle::make(name: 'self_nomination')
            ->afterStateUpdated(callback: function (Get $get, Set $set, bool $state): void {
                if (! $state && ! $get(path: 'nominator_threshold')) {
                    $set(path: 'nominator_threshold', state: 1);
                }
            })
            ->label(label: 'Allow self nomination')
            ->live();
    }

    public static function startsAtComponent(): DateTimePicker
    {
        return DateTimePicker::make(name: 'starts_at')
            ->required()
            ->seconds(condition: false);
    }

    public static function endsAtComponent(): DateTimePicker
    {
        return DateTimePicker::make(name: 'ends_at')
            ->after(date: 'starts_at')
            ->required()
            ->seconds(condition: false);
    }

    public static function timezoneComponent(): TimezonePicker
    {
        return TimezonePicker::make()
            ->label(label: 'Timezone')
            ->required();
    }
}
