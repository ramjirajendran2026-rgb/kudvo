<?php

namespace App\Actions\Election\Booth;

use App\Events\ElectorRevokedFromBoothEvent;
use App\Models\ElectionBoothToken;

class RevokeElectorFromBooth
{
    public static function execute(ElectionBoothToken $booth): void
    {
        $booth->update(attributes: ['current_elector_id' => null]);

        broadcast(new ElectorRevokedFromBoothEvent($booth->getKey()));
    }
}
