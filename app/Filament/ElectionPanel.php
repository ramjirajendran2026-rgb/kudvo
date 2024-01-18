<?php

namespace App\Filament;

use App\Facades\Kudvo;
use App\Models\Election;
use App\Models\Nomination;
use Filament\Panel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ElectionPanel extends Panel
{
    public function getElection(string $key): Election
    {
        $model = Election::class;

        $election = app($model)
            ->resolveRouteBinding($key);

        if ($election === null) {
            throw (new ModelNotFoundException())->setModel($model, [$key]);
        }

        return $election;
    }

    public function getPath(): string
    {
        return parent::getPath().'/{election}';
    }

    public function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        $parameters['election'] ??= Kudvo::getElection()?->getRouteKey();

        return parent::route($name, $parameters, $absolute);
    }
}
