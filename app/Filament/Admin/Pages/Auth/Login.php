<?php

namespace App\Filament\Admin\Pages\Auth;

use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BasePage;

class Login extends BasePage
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),

                $this->getPasswordFormComponent(),

                $this->getRememberFormComponent(),

                Turnstile::make(name: 'captcha')
                    ->theme('auto'),
            ]);
    }
}
