<?php

namespace App\Filament\User\Pages\Auth\EmailVerification;

use Filament\Facades\Filament;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt as BasePage;

class EmailVerificationPrompt extends BasePage
{
    public function getListeners(): array
    {
        return [
            'echo-private:App.Models.User.' . $this->getVerifiable()->id . ',Auth.EmailVerified' => 'checkStatus',
        ];
    }

    public function checkStatus(): void
    {
        if ($this->getVerifiable()->hasVerifiedEmail()) {
            redirect()->intended(Filament::getUrl());
        }
    }
}
