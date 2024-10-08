<?php

namespace App\Actions\Election\Booth;

use App\Events\ElectorAssignedToBoothEvent;
use App\Models\ElectionBoothToken;
use App\Models\Elector;

class AssignElectorToBooth
{
    public static function execute(ElectionBoothToken $booth, Elector | int $elector): void
    {
        $booth->currentElector()->associate($elector)->save();

        broadcast(new ElectorAssignedToBoothEvent($booth->getKey(), $booth->current_elector_id));
    }
}
