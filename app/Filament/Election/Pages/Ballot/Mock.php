<?php

namespace App\Filament\Election\Pages\Ballot;

use Illuminate\Contracts\Support\Htmlable;

class Mock extends Index
{
    protected static ?string $slug = 'ballot/mock';

    public function isMock(): bool
    {
        return true;
    }

    public function getHeading(): string | Htmlable
    {
        return '[MOCK] ' . parent::getHeading();
    }
}
