<?php

namespace App\Filament\User\Pages\Auth;

use App\Forms\UserForm;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Register as BasePage;

class Register extends BasePage
{
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
}
