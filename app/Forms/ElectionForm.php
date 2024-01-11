<?php

namespace App\Forms;

use App\Forms\Components\TimezonePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;

readonly class ElectionForm
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
            ->label(label: 'Election name / title')
            ->maxLength(length: 250)
            ->minLength(length: 5)
            ->required();
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
