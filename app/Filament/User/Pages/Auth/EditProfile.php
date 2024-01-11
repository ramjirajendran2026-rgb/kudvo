<?php

namespace App\Filament\User\Pages\Auth;

use App\Forms\UserForm;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Get;
use Filament\Pages\Auth\EditProfile as BasePage;
use Filament\Panel;

class EditProfile extends BasePage
{
    protected function getNameFormComponent(): Component
    {
        return UserForm::nameComponent();
    }

    protected function getEmailFormComponent(): Component
    {
        return UserForm::emailComponent()
            ->disabled(condition: fn (?User $user): bool => $user?->hasVerifiedEmail());
    }

    protected function getPasswordFormComponent(): Component
    {
        return UserForm::passwordComponent()
            ->autocomplete(autocomplete: 'new-password')
            ->dehydrated(condition: fn ($state): bool => filled($state))
            ->live(debounce: 500)
            ->required(condition: false);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return UserForm::passwordConfirmationComponent()
            ->visible(condition: fn (Get $get): bool => filled(value: $get(path: 'password')));
    }

    public static function getWithoutRouteMiddleware(Panel $panel): string|array
    {
        return self::getEmailVerifiedMiddleware(panel: $panel);
    }

    protected function getRedirectUrl(): ?string
    {
        return Filament::getUrl();
    }
}
