<?php

namespace App\Listeners\Nomination;

use App\Enums\NominatorStatus;
use App\Events\Nomination\Nominated;
use App\Notifications\Nomination\NominatorRequestNotification;

class SendNominatedNotifications
{
    public function handle(Nominated $event): void
    {
        $nominee = $event->getNominee();
        $proposer = $nominee->proposer;

        $notification = new NominatorRequestNotification(nominee: $nominee);
        foreach ($nominee->nominators as $index => $nominator) {
            if (
                $nominator->status != NominatorStatus::PENDING ||
                (! $nominee->self_nomination && $proposer->is(model: $nominator))
            ) {
                continue;
            }

            $nominator->elector?->notify(instance: $notification);
        }
    }
}
