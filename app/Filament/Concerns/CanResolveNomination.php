<?php

namespace App\Filament\Concerns;

use App\Facades\Kudvo;
use App\Models\Nomination;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CanResolveNomination
{
    public function resolveNomination(string $key, ?string $field = null): Nomination
    {
        $election = app(abstract: Nomination::class)
            ->resolveRouteBinding(value: $key, field: $field);

        if (blank($election)) {
            throw (new ModelNotFoundException())->setModel(model: Nomination::class, ids: [$key]);
        }

        return $election;
    }

    public function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        $parameters['nomination'] ??= Kudvo::getNomination()?->getRouteKey();

        return parent::route($name, $parameters, $absolute);
    }

    public function getPath(): string
    {
        return parent::getPath().'/{nomination}';
    }
}
