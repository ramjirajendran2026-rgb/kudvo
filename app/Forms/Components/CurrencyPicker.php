<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Select;
use Illuminate\Support\Str;
use Squire\Models\Currency;

class CurrencyPicker extends Select
{
    public static function make(string $name = 'currency'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(
            options: Currency::all()
                ->sortBy(callback: 'name')
                ->mapWithKeys(callback: fn (Currency $currency) => [$currency->code_alphabetic => Str::upper(value: $currency->code_alphabetic)])
                ->toArray()
        );
        $this->optionsLimit(limit: Currency::count());
        $this->searchable();
    }
}
