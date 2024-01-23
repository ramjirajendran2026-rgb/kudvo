<?php

namespace App\Filament;

use App\Facades\Kudvo;
use App\Filament\Concerns\CanResolveNomination;
use App\Filament\Contracts\ResolvesNomination;
use App\Models\Nomination;
use Filament\Panel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NominationPanel extends Panel implements ResolvesNomination
{
    use CanResolveNomination;
}
