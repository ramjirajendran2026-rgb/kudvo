<?php

namespace App\Notifications\Contracts;

use App\Enums\MailMessagePurpose;
use App\Enums\SmsMessagePurpose;

interface HasMailMessagePurpose
{
    public function getMailMessagePurpose(object $notifiable): MailMessagePurpose;
}
