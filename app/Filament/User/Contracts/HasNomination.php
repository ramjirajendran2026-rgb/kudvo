<?php

namespace App\Filament\User\Contracts;

use App\Models\Nomination;

interface HasNomination
{
    public function getNomination(): Nomination;
}
