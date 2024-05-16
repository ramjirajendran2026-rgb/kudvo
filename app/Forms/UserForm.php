<?php

namespace App\Forms;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

readonly class UserForm
{
    public static function nameComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->autofocus()
            ->label(label: __('filament.user.user-resource.form.name.label'))
            ->maxLength(length: 150)
            ->required();
    }

    public static function emailComponent(): TextInput
    {
        return TextInput::make(name: 'email')
            ->email()
            ->label(label: __('filament.user.user-resource.form.email.label'))
            ->maxLength(length: 255)
            ->required()
            ->rule(rule: 'email:rfc,dns')
            ->unique(table: app(abstract: User::class)->getTable(), ignoreRecord: true);
    }

    public static function passwordComponent(): TextInput
    {
        return TextInput::make(name: 'password')
            ->label(label: __('filament.user.user-resource.form.password.label'))
            ->password()
            ->required()
            ->rule(rule: Password::default())
            ->same(statePath: 'passwordConfirmation');
    }

    public static function passwordConfirmationComponent(): TextInput
    {
        return TextInput::make(name: 'passwordConfirmation')
            ->dehydrated(condition: false)
            ->label(label: __('filament.user.user-resource.form.password_confirmation.label'))
            ->password()
            ->required();
    }
}
