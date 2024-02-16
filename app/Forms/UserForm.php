<?php

namespace App\Forms;

use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

readonly class UserForm
{
    public static function nameComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->autofocus()
            ->label(label: 'Your name')
            ->maxLength(length: 150)
            ->required();
    }

    public static function emailComponent(): TextInput
    {
        return TextInput::make(name: 'email')
            ->email()
            ->label(label: 'Email address')
            ->maxLength(length: 255)
            ->required()
            ->rule(rule: 'email:rfc,dns')
            ->unique(ignoreRecord: true);
    }

    public static function passwordComponent(): TextInput
    {
        return TextInput::make(name: 'password')
            ->label(label: 'Password')
            ->password()
            ->required()
            ->rule(rule: Password::default())
            ->same(statePath: 'passwordConfirmation');
    }

    public static function passwordConfirmationComponent(): TextInput
    {
        return TextInput::make(name: 'passwordConfirmation')
            ->dehydrated(condition: false)
            ->label(label: 'Confirm password')
            ->password()
            ->required();
    }
}
