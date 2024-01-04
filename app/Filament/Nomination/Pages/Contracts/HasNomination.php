<?php

namespace App\Filament\Nomination\Pages\Contracts;

use App\Models\Nomination;

interface HasNomination
{
    public function getNomination(): Nomination;
}
