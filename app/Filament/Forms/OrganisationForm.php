<?php

namespace App\Filament\Forms;

use Countries;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use OmarHaris\FilamentTimezoneField\Forms\Components\Timezone;

readonly class OrganisationForm
{
    public static function countryComponent(): Select
    {
        return Select::make(name: 'country')
            ->label(label: __(key: 'filament/forms/organisation.country.label'))
            ->options(options: Countries::lookup())
            ->optionsLimit(limit: 500)
            ->required()
            ->searchable();
    }

    public static function nameComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->label(label: __(key: 'filament/forms/organisation.name.label'))
            ->maxLength(length: 60)
            ->minLength(length: 4)
            ->required();
    }

    public static function timezoneComponent(): Timezone
    {
        return Timezone::make(name: 'timezone')
            ->label(label: __(key: 'filament/forms/organisation.timezone.label'))
            ->required()
            ->searchable();
    }
}
