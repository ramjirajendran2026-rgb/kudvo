<?php

namespace App\Filament\Contracts;

use App\Models\Nomination;

interface ResolvesNomination
{
    public function resolveNomination(string $key): Nomination;
}
