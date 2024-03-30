<?php

namespace App\Notifications\Contracts;

use App\Enums\SmsMessagePurpose;

interface HasSmsMessagePurpose
{
    public function getSmsMessagePurpose(object $notifiable): SmsMessagePurpose;
}
