<?php

namespace App\Notifications\Contracts;

use App\Enums\WhatsAppMessagePurpose;

interface HasWhatsAppMessagePurpose
{
    public function getWhatsAppMessagePurpose(object $notifiable): WhatsAppMessagePurpose;
}
