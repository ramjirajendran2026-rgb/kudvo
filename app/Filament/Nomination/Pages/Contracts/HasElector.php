<?php

namespace App\Filament\Nomination\Pages\Contracts;

use App\Models\Elector;
use Illuminate\Contracts\Auth\Authenticatable;

interface HasElector
{
    public function getElector(): Elector|Authenticatable;
}
