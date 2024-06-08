<?php

namespace App\Actions\Election\Booth;

use App\Events\ElectorAssignedToBoothEvent;
use App\Facades\Kudvo;
use App\Models\Elector;

class UpdateOnElectorLogin
{
    public static function execute(Elector $elector)
    {
        if (! Kudvo::isBoothDevice()) {
            return;
        }

        $boothToken = Kudvo::getElectionBoothToken();

        if ($boothToken?->current_elector_id === $elector->getKey()) {
            return;
        }

        $boothToken?->update([
            'current_elector_id' => $elector->getKey(),
        ]);

        broadcast(new ElectorAssignedToBoothEvent($boothToken?->getKey(), $elector->getKey()))
            ->toOthers();
    }
}
