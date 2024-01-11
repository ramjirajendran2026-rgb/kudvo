<?php

namespace App\Forms;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

readonly class ElectorForm
{
    public static function emailComponent(): TextInput
    {
        return TextInput::make(name: 'email')
            ->email()
            ->label(label: 'Email address')
            ->maxLength(length: 100);
    }

    public static function firstNameComponent(): TextInput
    {
        return TextInput::make(name: 'first_name')
            ->label(label: 'First name')
            ->maxLength(length: 100);
    }

    public static function groupsComponent(): TagsInput
    {
        return TagsInput::make(name: 'groups')
            ->label(label:'Groups')
            ->separator();
    }

    public static function lastNameComponent(): TextInput
    {
        return TextInput::make(name: 'last_name')
            ->label(label: 'Last name')
            ->maxLength(length: 100);
    }

    public static function membershipNumberComponent(): TextInput
    {
        return TextInput::make(name: 'membership_number')
            ->label(label: 'Membership number')
            ->maxLength(length: 50)
            ->required()
            ->unique(ignoreRecord: true);
    }

    public static function phoneComponent(): PhoneInput
    {
        return PhoneInput::make(name: 'phone')
            ->defaultCountry(value: config(key: 'app.default_phone_country'))
            ->label(label: 'Phone number')
            ->useFullscreenPopup()
            ->validateFor();
    }

    public static function titleComponent(): TextInput
    {
        return TextInput::make(name: 'title')
            ->label(label: 'Salutation')
            ->maxLength(length: 20);
    }
}
