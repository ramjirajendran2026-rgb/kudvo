<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Select;
use Nnjeim\World\Models\Timezone;

class TimezonePicker extends Select
{
    public static function make(string $name = 'timezone'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(state: request()->ipinfo?->timezone);

        $this->options(
            options: Timezone::all()
                ->sortBy(callback: fn ($timezone) => now($timezone->name)->format('O'))
                ->mapWithKeys(callback: fn ($timezone): array => [$timezone->name => static::getDisplayLabel($timezone->name)])
                ->toArray()
        );

        $this->optionsLimit(limit: 5000);

        $this->searchable();
    }

    public static function getDisplayLabel(string $timezone): string
    {
        $now = now($timezone);

        $tz = "(UTC {$now->format('P')}) ";
        $tz .= str($timezone)
            ->after('/')
            ->replace('/', ', ')
            ->replace('_', ' ');

        $abb = str($now->format('T'));

        if (! $abb->startsWith(['-', '+'])) {
            $tz .= " ({$abb})";
        }

        return $tz;
    }
}
