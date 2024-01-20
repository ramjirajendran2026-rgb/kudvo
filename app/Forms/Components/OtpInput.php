<?php

namespace App\Forms\Components;

use Filament\Forms\Components\TextInput;
use Jenssegers\Agent\Agent;

class OtpInput extends TextInput
{
    protected string $view = 'forms.components.otp-input';

    protected function setUp(): void
    {
        parent::setUp();

        $this->autocomplete(autocomplete: 'one-time-password');

//        $this->readOnly(condition: fn (Agent $agent): bool => ! ($agent->isiPhone() && $agent->isSafari()));
    }
}
