<?php

namespace App\Filament\Concerns;

use App\Facades\Kudvo;
use App\Models\Election;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CanResolveElection
{
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
        return parent::getPath().'/{election}';
    }
}
