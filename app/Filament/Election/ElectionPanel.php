<?php

namespace App\Filament\Election;

use App\Enums\ElectionPanelState;
use App\Facades\Kudvo;
use App\Filament\Base\Contracts\ResolvesElection;
use App\Models\Election;
use Filament\Panel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ElectionPanel extends Panel implements ResolvesElection
{
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

    public function resolveElection(string $key, ?string $field = null): Election
    {
        $election = app(abstract: Election::class)
            ->resolveRouteBinding(value: $key, field: $field);

        if (blank($election)) {
            throw (new ModelNotFoundException())->setModel(model: Election::class, ids: [$key]);
        }

        return $election;
    }

    public function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        $parameters['election'] ??= Kudvo::getElection()?->getRouteKey();

        return parent::route($name, $parameters, $absolute);
    }

    public function getPath(): string
    {
        return parent::getPath() . '/{election}';
    }
}
