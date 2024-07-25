<?php

namespace App\Services\TwentyFourSevenSms;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Notification;

class SmsMessageSent
{
    use Dispatchable;

    public function __construct(
        public object $notifiable,
        public Notification $notification,
        public string $result,
    ) {}
}
