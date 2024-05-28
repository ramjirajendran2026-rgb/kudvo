<?php

namespace App\Filament\User\Pages\Auth;

use App\Forms\UserForm;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BasePage;

class Register extends BasePage
{
    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                $this->getEmailFormComponent(),

                $this->getPasswordFormComponent(),

                $this->getPasswordConfirmationFormComponent(),
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
}
