<?php

namespace App\Notifications\Contracts;

use App\Enums\MailMessagePurpose;

interface HasMailMessagePurpose
{
    public function getMailMessagePurpose(object $notifiable): MailMessagePurpose;
}
