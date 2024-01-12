<?php

namespace App\Filament\Contracts;

use App\Models\Nomination;

interface HasNomination
{
    public function getNomination(): Nomination;
}
