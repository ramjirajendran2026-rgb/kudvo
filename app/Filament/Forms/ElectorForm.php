<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

readonly class ElectorForm
{
    public static function emailComponent(): TextInput
    {
        return TextInput::make(name: 'email')
            ->email()
            ->label(label: __(key: 'filament/forms/elector.email.label'))
            ->maxLength(length: 100);
    }

    public static function firstNameComponent(): TextInput
    {
        return TextInput::make(name: 'first_name')
            ->label(label: __(key: 'filament/forms/elector.first_name.label'))
            ->maxLength(length: 100);
    }

    public static function groupsComponent()
    {
        return TagsInput::make(name: 'groups')
            ->label(label: __(key: 'filament/forms/elector.groups.label'));
    }

    public static function lastNameComponent(): TextInput
    {
        return TextInput::make(name: 'last_name')
            ->label(label: __(key: 'filament/forms/elector.last_name.label'))
            ->maxLength(length: 100);
    }

    public static function membershipNumberComponent(): TextInput
    {
        return TextInput::make(name: 'membership_number')
            ->label(label: __(key: 'filament/forms/elector.membership_number.label'))
            ->maxLength(length: 50)
            ->required()
            ->unique(ignoreRecord: true);
    }

    public static function phoneComponent(): PhoneInput
    {
        return PhoneInput::make(name: 'phone')
            ->defaultCountry(value: config(key: 'app.default_phone_country'))
            ->label(label: __(key: 'filament/forms/elector.phone.label'))
            ->useFullscreenPopup()
            ->validateFor();
    }

    public static function titleComponent(): TextInput
    {
        return TextInput::make(name: 'title')
            ->label(label: __(key: 'filament/forms/elector.title.label'))
            ->maxLength(length: 20);
    }
}
