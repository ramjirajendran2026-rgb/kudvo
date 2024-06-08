<?php

namespace App\Actions\Election\Booth;

use App\Events\ElectorRevokedFromBoothEvent;
use App\Facades\Kudvo;

class UpdateOnElectorLogout
{
    public static function execute(): void
    {
        if (! Kudvo::isBoothDevice()) {
            return;
        }

        $boothToken = Kudvo::getElectionBoothToken();
        if (blank($boothToken?->current_elector_id)) {
            return;
        }

        $boothToken->update(attributes: ['current_elector_id' => null]);

        broadcast(new ElectorRevokedFromBoothEvent($boothToken->getKey()))
            ->toOthers();
    }
}
