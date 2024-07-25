<?php

namespace App\Forms\Components;

use Filament\Forms\Components\TextInput;

class MoneyInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrateStateUsing(callback: fn (?float $state): ?int => $state ? ($state * 100) : $state);

        $this->formatStateUsing(callback: fn (?int $state): ?float => $state ? ($state * 0.01) : $state);
    }
}
