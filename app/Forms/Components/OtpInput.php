<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\TextInput;
use Jenssegers\Agent\Agent;

class OtpInput extends TextInput
{
    protected string $view = 'forms.components.otp-input';

    protected Closure|bool $autoFill = true;

    protected ?string $verifyActionName = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autocomplete(autocomplete: 'one-time-password');

//        $this->readOnly(condition: fn (Agent $agent): bool => ! ($agent->isiPhone() && $agent->isSafari()));
    }

    public function autoFill(Closure|bool $condition = true): static
    {
        $this->autoFill = $condition;

        return $this;
    }

    public function shouldAutoFill(): bool
    {
        return $this->evaluate(value: $this->autoFill);
    }

    public function verifyActionName(string $value): static
    {
        $this->verifyActionName = $value;

        return $this;
    }

    public function getVerifyActionName(): ?string
    {
        return $this->verifyActionName;
    }
}
