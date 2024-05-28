<?php

namespace App\Filament\User\Pages\Auth;

use App\Forms\UserForm;
use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BasePage;
use Illuminate\Validation\ValidationException;

class Register extends BasePage
{
    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                $this->getEmailFormComponent(),

                $this->getPasswordFormComponent(),

                $this->getPasswordConfirmationFormComponent(),

                Turnstile::make(name: 'captcha'),
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return UserForm::nameComponent();
    }

    protected function getEmailFormComponent(): Component
    {
        return UserForm::emailComponent();
    }

    protected function getPasswordFormComponent(): Component
    {
        return UserForm::passwordComponent();
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return UserForm::passwordConfirmationComponent();
    }

    protected function onValidationError(ValidationException $exception): void
    {
        $this->dispatch('reset-captcha');

        parent::onValidationError($exception);
    }
}
