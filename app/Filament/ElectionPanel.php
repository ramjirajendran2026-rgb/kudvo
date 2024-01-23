<?php

namespace App\Filament;

use App\Facades\Kudvo;
use App\Filament\Concerns\CanResolveElection;
use App\Filament\Contracts\ResolvesElection;
use App\Models\Election;
use App\Models\Nomination;
use Filament\Panel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ElectionPanel extends Panel implements ResolvesElection
{
    use CanResolveElection;
}
