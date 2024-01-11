<?php

namespace App\Filament\User\Contracts;

use App\Models\Election;

interface HasElection
{
    public function getElection(): Election;
}
