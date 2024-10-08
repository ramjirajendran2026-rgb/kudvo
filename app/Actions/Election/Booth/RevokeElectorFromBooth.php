<?php

namespace App\Actions\Election\Booth;

use App\Events\ElectorRevokedFromBoothEvent;
use App\Models\ElectionBoothToken;

class RevokeElectorFromBooth
{
    public static function execute(ElectionBoothToken $booth): void
    {
        if (blank($booth->current_elector_id)) {
            return;
        }

        $booth->currentElector()->dissociate()->save();

        broadcast(new ElectorRevokedFromBoothEvent($booth->getKey()));
    }
}
