<?php

namespace App\Filament\Base\Contracts;

use App\Models\Nomination;

interface ResolvesNomination
{
    public function resolveNomination(string $key): Nomination;
}
