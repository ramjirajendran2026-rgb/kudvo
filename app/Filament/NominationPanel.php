<?php

namespace App\Filament;

use App\Facades\Kudvo;
use App\Models\Nomination;
use Filament\Panel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NominationPanel extends Panel
{
    public function getNomination(string $key): Nomination
    {
        $model = Nomination::class;

        $nomination = app($model)
            ->resolveRouteBinding($key);

        if ($nomination === null) {
            throw (new ModelNotFoundException())->setModel($model, [$key]);
        }

        return $nomination;
    }

    public function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        $parameters['nomination'] ??= Kudvo::getNomination()?->getRouteKey();

        return parent::route($name, $parameters, $absolute);
    }
}
