<?php

namespace App\Forms;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

readonly class MemberForm
{
    public static function emailComponent(): TextInput
    {
        return TextInput::make(name: 'email')
            ->email()
            ->label(label: __('filament.user.elector-resource.form.email.label'))
            ->maxLength(length: 100)
            ->rule(rule: 'email:rfc,dns');
    }

    public static function firstNameComponent(): TextInput
    {
        return TextInput::make(name: 'first_name')
            ->label(label: __('filament.user.elector-resource.form.first_name.label'))
            ->maxLength(length: 100);
    }

    public static function isActiveComponent(): Toggle
    {
        return Toggle::make(name: 'is_active')
            ->default(true)
            ->label('Is Active?');
    }

    public static function lastNameComponent(): TextInput
    {
        return TextInput::make(name: 'last_name')
            ->label(label: __('filament.user.elector-resource.form.last_name.label'))
            ->maxLength(length: 100);
    }

    public static function membershipNumberComponent(): TextInput
    {
        return TextInput::make(name: 'membership_number')
            ->label(label: __('filament.user.elector-resource.form.membership_number.label'))
            ->maxLength(length: 50)
            ->required();
    }

    public static function phoneComponent(): PhoneInput
    {
        return PhoneInput::make(name: 'phone')
            ->displayNumberFormat(PhoneInputNumberType::E164)
            ->focusNumberFormat(PhoneInputNumberType::E164)
            ->inputNumberFormat(PhoneInputNumberType::E164)
            ->label(label: __('filament.user.elector-resource.form.phone.label'))
            ->validateFor();
    }

    public static function titleComponent(): TextInput
    {
        return TextInput::make(name: 'title')
            ->datalist(options: ['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.'])
            ->label(label: __('filament.user.elector-resource.form.title.label'))
            ->maxLength(length: 20);
    }
}
