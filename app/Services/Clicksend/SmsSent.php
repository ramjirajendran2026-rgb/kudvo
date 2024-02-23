<?php

namespace App\Services\Clicksend;

use App\Services\Clicksend\Data\SendSmsResponseData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Notification;

class SmsSent
{
    use Dispatchable;

    public function __construct(
        public object $notifiable,
        public Notification $notification,
        public SendSmsResponseData $response
    ) {
    }
}
