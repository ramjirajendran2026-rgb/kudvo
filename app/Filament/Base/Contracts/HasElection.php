<?php

namespace App\Filament\Base\Contracts;

use App\Models\Election;

interface HasElection
{
    public function getElection(): Election;
}
