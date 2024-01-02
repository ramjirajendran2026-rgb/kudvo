<?php

namespace App\Forms\Components;

use OmarHaris\FilamentTimezoneField\Forms\Components\Timezone;

class TimezonePicker extends Timezone
{
    public static function make(string $name = 'timezone'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->searchable();
    }
}
