<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Select;
use Nnjeim\World\Models\Country;

class CountryPicker extends Select
{
    public static function make(string $name = 'country'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(state: request()->ipinfo?->country);

        $this->options(options: Country::all()->pluck('name', 'iso2')->toArray());

        $this->optionsLimit(limit: 5000);

        $this->searchable();
    }
}
