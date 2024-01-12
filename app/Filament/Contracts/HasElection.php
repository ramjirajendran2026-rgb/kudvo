<?php

namespace App\Filament\Contracts;

use App\Models\Election;

interface HasElection
{
    public function getElection(): Election;
}
