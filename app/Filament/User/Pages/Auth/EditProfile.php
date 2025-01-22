<?php

namespace App\Filament\User\Pages\Auth;

use App\Forms\UserForm;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\EditProfile as BasePage;
use Filament\Panel;

class EditProfile extends BasePage
{
    public ?string $currentUrl = null;

    public ?string $previousUrl = null;

    public function mount(): void
    {
        parent::mount();

        $this->currentUrl = url()->current();
        $this->previousUrl = url()->previous();
    }

    protected function getNameFormComponent(): Component
    {
        return UserForm::nameComponent();
    }

    protected function getEmailFormComponent(): Component
    {
        return UserForm::emailComponent()
            ->disabled(condition: fn (?User $user): bool => $user?->hasVerifiedEmail());
    }

    public static function getWithoutRouteMiddleware(Panel $panel): string | array
    {
        return self::getEmailVerifiedMiddleware(panel: $panel);
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->previousUrl !== $this->currentUrl ? $this->previousUrl : Filament::getUrl();
    }
}
