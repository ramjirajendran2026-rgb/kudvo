<?php

namespace App\Services\WhatsApp;

use App\Services\WhatsApp\Data\SendWhatsAppMessageResponseData;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class WhatsAppMessageSent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly object $notifiable,
        public readonly Notification $notification,
        public readonly SendWhatsAppMessageResponseData $response
    ) {}
}
