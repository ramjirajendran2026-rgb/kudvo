<?php

namespace App\Forms;

use App\Forms\Components\TimezonePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;

readonly class ElectionForm
{
    public static function boothStartsAtComponent(): DateTimePicker
    {
        return DateTimePicker::make(name: 'booth_starts_at')
            ->label(label: __('filament.user.election-resource.form.booth_starts_at.label'))
            ->required()
            ->seconds(condition: false);
    }

    public static function boothEndsAtComponent(): DateTimePicker
    {
        return DateTimePicker::make(name: 'booth_ends_at')
            ->after(date: 'booth_starts_at')
            ->label(label: __('filament.user.election-resource.form.booth_ends_at.label'))
            ->required()
            ->seconds(condition: false);
    }

    public static function descriptionComponent(): RichEditor
    {
        return RichEditor::make(name: 'description')
            ->label(label: __('filament.user.election-resource.form.description'))
            ->maxLength(length: 2500);
    }

    public static function nameComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->label(label: __(key: 'filament.user.election-resource.form.name.label'))
            ->maxLength(length: 250)
            ->minLength(length: 5)
            ->required();
    }

    public static function startsAtComponent(): DateTimePicker
    {
        return DateTimePicker::make(name: 'starts_at')
            ->label(label: __('filament.user.election-resource.form.starts_at.label'))
            ->required()
            ->seconds(condition: false);
    }

    public static function endsAtComponent(): DateTimePicker
    {
        return DateTimePicker::make(name: 'ends_at')
            ->after(date: 'starts_at')
            ->label(label: __('filament.user.election-resource.form.ends_at.label'))
            ->required()
            ->seconds(condition: false);
    }

    public static function timezoneComponent(): TimezonePicker
    {
        return TimezonePicker::make()
            ->label(label: __('filament.user.election-resource.form.timezone.label'))
            ->required();
    }
}
