<?php

namespace App\Filament;

use App\Filament\Concerns\CanResolveElection;
use App\Filament\Contracts\ResolvesElection;
use Filament\Panel;

class BallotPanel extends Panel implements ResolvesElection
{
    use CanResolveElection;
}
