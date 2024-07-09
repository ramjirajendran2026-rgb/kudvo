<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Toggle;

class FeatureToggle extends Toggle
{
    protected string $view = 'forms.components.feature-toggle';

    protected bool|Closure $isAddOn = false;

    protected string|Closure|null $addOnTooltip = null;

    protected int|Closure $featureFee = 0;

    protected int|Closure $electorFee = 0;

    protected string|Closure|null $feeCurrency = null;

    protected bool|Closure $shouldHideAddOnPrice = false;

    public function addOn(bool|Closure $condition = true, int|Closure $featureFee = 0, int|Closure $electorFee = 0, string|Closure|null $feeCurrency = null, bool|Closure $hideAddOnPrice = false): static
    {
        $this->isAddOn = $condition;
        $this->addOnTooltip(tooltip: $condition ? 'This is an add-on feature. Additional charges may apply.' : null);
        $this->featureFee(fee: $featureFee);
        $this->electorFee(fee: $electorFee);
        $this->feeCurrency(currency: $feeCurrency);
        $this->hideAddOnPrice(condition: $hideAddOnPrice);

        $this->hintColor(color: 'primary');
        $this->hintIcon(
            icon: function (self $component) {
                if (! $component->isAddOn() || $component->shouldHideAddOnPrice()) {
                    return null;
                }

                return ($this->getElectorFee() || $this->getFeatureFee()) ? 'heroicon-o-banknotes' : null;
            },
            tooltip: function (self $component) {
                if (! $component->isAddOn() || $component->shouldHideAddOnPrice()) {
                    return null;
                }

                return collect(value: [
                    $component->getElectorFee() ? money(amount: $component->getElectorFee(), currency: $component->getFeeCurrency()).'/elector' : null,
                    $component->getFeatureFee() ? money(amount: $component->getFeatureFee(), currency: $component->getFeeCurrency()).'' : null,
                ])->filter(callback: fn ($fee): bool => filled(value: $fee))->implode(value: ' + ') ?: null;
            }
        );

        return $this;
    }

    public function addOnTooltip(string|Closure|null $tooltip): static
    {
        $this->addOnTooltip = $tooltip;

        return $this;
    }

    public function featureFee(int|Closure $fee): static
    {
        $this->featureFee = $fee;

        return $this;
    }

    public function electorFee(int|Closure $fee): static
    {
        $this->electorFee = $fee;

        return $this;
    }

    public function feeCurrency(string|Closure|null $currency): static
    {
        $this->feeCurrency = $currency;

        return $this;
    }

    public function hideAddOnPrice(bool|Closure $condition = true): static
    {
        $this->shouldHideAddOnPrice = $condition;

        return $this;
    }

    public function isAddOn(): bool
    {
        return $this->evaluate($this->isAddOn);
    }

    public function getAddOnTooltip(): ?string
    {
        return $this->evaluate($this->addOnTooltip);
    }

    public function getFeatureFee(): int
    {
        return $this->evaluate($this->featureFee);
    }

    public function getElectorFee(): int
    {
        return $this->evaluate($this->electorFee);
    }

    public function getFeeCurrency(): ?string
    {
        return $this->evaluate($this->feeCurrency);
    }

    public function shouldHideAddOnPrice(): bool
    {
        return $this->evaluate($this->shouldHideAddOnPrice);
    }
}
