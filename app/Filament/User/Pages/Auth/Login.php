<?php

namespace App\Filament\User\Pages\Auth;

use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BasePage;
use Illuminate\Validation\ValidationException;

class Login extends BasePage
{
    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                $this->getEmailFormComponent(),

                $this->getPasswordFormComponent(),

                $this->getRememberFormComponent(),

                Turnstile::make(name: 'captcha')
                    ->theme('auto'),
            ]);
    }

    protected function onValidationError(ValidationException $exception): void
    {
        $this->dispatch('reset-captcha');

        parent::onValidationError($exception);
    }
}
