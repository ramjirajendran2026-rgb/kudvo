<?php

namespace App\Listeners;

use App\Events\NomineeNominated;
use App\Notifications\NomineeAcceptanceNotification;
use Illuminate\Support\Facades\Notification;

class SendNomineeNominatedNotifications
{
    public function __construct()
    {
    }

    public function handle(NomineeNominated $event): void
    {
        $nominee = $event->getNominee();

        if (! $nominee->self_nomination) {
            Notification::route(channel: 'mail', route: $nominee->routeNotificationFor(driver: 'mail'))
                ->notify(notification: new NomineeAcceptanceNotification(nominee: $nominee->fresh()));
        }
    }
}
