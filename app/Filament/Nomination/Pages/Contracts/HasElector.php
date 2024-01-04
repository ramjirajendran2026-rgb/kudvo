<?php

namespace App\Filament\Nomination\Pages\Contracts;

use App\Models\Elector;

interface HasElector
{
    public function getElector(): Elector;
}
