<?php

namespace App\Filament\Election\Pages\Concerns;

use App\Models\Elector;
use Filament\Facades\Filament;

trait InteractsWithElector
{
    public function getElector(): Elector
    {
        /** @var Elector $elector */
        $elector = Filament::auth()->user();

        return $elector;
    }
}
