<?php

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationSent;

class LogNotification
{
    public function __construct()
    {
    }

    public function handle(NotificationSent $event): void
    {

    }
}
