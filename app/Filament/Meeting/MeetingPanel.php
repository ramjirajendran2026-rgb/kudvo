<?php

namespace App\Filament\Meeting;

use App\Facades\Kudvo;
use App\Filament\Base\Contracts\ResolvesMeeting;
use App\Models\Meeting;
use Filament\Panel;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MeetingPanel extends Panel implements ResolvesMeeting
{
    public function resolveMeeting(string $key, ?string $field = null): Meeting
    {
        $meeting = app(abstract: Meeting::class)
            ->resolveRouteBinding(value: $key, field: $field);

        if (blank($meeting)) {
            throw (new ModelNotFoundException)->setModel(model: Meeting::class, ids: [$key]);
        }

        return $meeting;
    }

    public function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        $parameters['meeting'] ??= Kudvo::getMeeting()?->getRouteKey();

        return parent::route($name, $parameters, $absolute);
    }

    public function getPath(): string
    {
        return parent::getPath() . '/{meeting}';
    }
}
