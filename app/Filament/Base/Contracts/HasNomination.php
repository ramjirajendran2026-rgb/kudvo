<?php

namespace App\Filament\Base\Contracts;

use App\Models\Nomination;

interface HasNomination
{
    public function getNomination(): Nomination;
}
