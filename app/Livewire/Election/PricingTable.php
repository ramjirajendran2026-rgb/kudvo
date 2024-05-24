<?php

namespace App\Livewire\Election;

use App\Models\ElectionPlan;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PricingTable extends Component
{
    public string $currency = 'USD';

    public array $supportedCurrencies = [];

    public function mount(): void
    {
        $this->currency = config('app.default_currency');
        $this->supportedCurrencies = config('app.supported_currencies');
    }

    #[Computed]
    public function plans(): Collection
    {
        return ElectionPlan::where('currency', $this->currency)
            ->get();
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }
}
