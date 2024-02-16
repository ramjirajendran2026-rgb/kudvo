<?php

namespace App\Listeners;

use App\Events\NomineeNominated;
use App\Notifications\Nomination\NomineeAcceptanceNotification;
use App\Notifications\Nomination\ProposerAcceptanceNotification;
use App\Notifications\Nomination\SeconderAcceptanceNotification;

class SendNomineeNominatedNotifications
{
    public function handle(NomineeNominated $event): void
    {
        $nominee = $event->getNominee();

        if (! $nominee->self_nomination) {
            $nominee->elector?->notify(instance: new NomineeAcceptanceNotification(nominee: $nominee));
        }
        elseif ($proposer = $nominee->proposer) {
            $proposer->elector?->notify(instance: new ProposerAcceptanceNotification(nominee: $nominee));
        }

        foreach ($nominee->seconders as $seconder) {
            $seconder->elector?->notify(instance: new SeconderAcceptanceNotification(nominee: $nominee, seconder: $seconder));
        }
    }
}
