<?php

namespace App\Filament;

use App\Enums\ElectionPanelState;
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

    protected ?ElectionPanelState $state = null;

    public function setState(?ElectionPanelState $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getState(): ?ElectionPanelState
    {
        return $this->state;
    }
}
