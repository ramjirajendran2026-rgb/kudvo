<?php

namespace App\Filament\Base\Contracts;

use App\Models\Election;

interface ResolvesElection
{
    public function resolveElection(string $key): Election;
}
