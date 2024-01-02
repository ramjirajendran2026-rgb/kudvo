<?php

namespace App\Forms\Components;

use Countries;
use Filament\Forms\Components\Select;

class CountryPicker extends Select
{
    public static function make(string $name = 'country'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(options: Countries::lookup());

        $this->optionsLimit(limit: 5000);

        $this->searchable();
    }
}
