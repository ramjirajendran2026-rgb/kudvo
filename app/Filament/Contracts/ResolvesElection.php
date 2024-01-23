<?php

namespace App\Filament\Contracts;

use App\Models\Election;

interface ResolvesElection
{
    public function resolveElection(string $key): Election;
}
