<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Contracts\CanHaveNumericState;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Contracts;
use Filament\Forms\Components\Concerns;
use Jenssegers\Agent\Agent;

class OtpInput extends Field implements Contracts\CanHaveNumericState
{
    use Concerns\CanBeAutocapitalized;
    use Concerns\CanBeAutocompleted;
    use Concerns\CanBeReadOnly;
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasInputMode;
    use Concerns\HasStep;

    protected string $view = 'forms.components.otp-input';

    protected int | Closure $length = 4;

    protected bool | Closure $isNumeric = true;

    protected bool | Closure | null $safari = null;

    protected bool | Closure $autoFillOnly = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autocomplete(autocomplete: 'one-time-password');
        $this->autofocus();
        $this->hiddenLabel();
    }

    public function length(int | Closure $length): static
    {
        $this->length = $length;

        $this->rule(static function (self $component): string {
            $length = $component->getLength();

            if ($component->isNumeric()) {
                return "digits:{$length}";
            }

            return "size:{$length}";
        }, static fn (self $component): bool => filled($component->getLength()));

        return $this;
    }

    public function getLength(): int
    {
        return $this->evaluate($this->length);
    }

    public function numeric(bool | Closure $condition = true): static
    {
        $this->isNumeric = $condition;

        $this->inputMode(static fn (): ?string => $condition ? 'decimal' : null);
        $this->rule('numeric', $condition);
        $this->step(static fn (): ?string => $condition ? 'any' : null);

        return $this;
    }

    public function isNumeric(): bool
    {
        return (bool) $this->evaluate($this->isNumeric);
    }

    public function safari(bool|Closure $condition = true): static
    {
        $this->safari = $condition;

        return $this;
    }

    public function isSafari(): bool
    {
        if (! is_null($this->safari)) {
            return (bool) $this->evaluate($this->safari);
        }

        return $this->evaluate(fn (Agent $agent): bool => $agent->isSafari());
    }

    public function autoFillOnly(bool|Closure $condition = true): static
    {
        $this->autoFillOnly = $condition;

        return $this;
    }

    public function isAutoFillOnly(): bool
    {
        return (bool) $this->evaluate($this->autoFillOnly);
    }
}
