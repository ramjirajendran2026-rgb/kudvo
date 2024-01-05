<?php

namespace App\Listeners\Nomination;

use App\Console\NominatorStatusEnum;
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
                $nominator->status != NominatorStatusEnum::PENDING ||
                (! $nominee->self_nomination && $proposer->is(model: $nominator))
            ) {
                continue;
            }

            $nominator->elector?->notify(instance: $notification);
        }
    }
}
