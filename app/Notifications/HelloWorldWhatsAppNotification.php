<?php

namespace App\Notifications;

use App\Services\WhatsApp\Messages\WhatsAppMessage;
use App\Services\WhatsApp\Messages\WhatsAppMessageFactory;
use App\Services\WhatsApp\WhatsAppChannel;
use Illuminate\Notifications\Notification;

class HelloWorldWhatsAppNotification extends Notification
{
    public function __construct() {}

    public function via($notifiable): array
    {
        return [WhatsAppChannel::NAME];
    }

    public function toWhatsapp($notifiable): string | WhatsAppMessage
    {
        return WhatsAppMessageFactory::template('hello_world', 'en_US');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
