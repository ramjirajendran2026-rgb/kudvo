<?php

namespace App\Notifications\Channels\Concerns;

use Illuminate\Notifications\Notification;

trait Whatsappable
{
    protected function getWhatsAppRoute(object $notifiable, Notification $notification): ?string
    {
        if (method_exists($notifiable, method: 'routeNotificationFor')) {
            return $notifiable->routeNotificationFor('whatsapp', $notification)
                ?: $notifiable->routeNotificationFor('sms', $notification);
        }

        return null;
    }

    protected function getWhatsAppMessage(object $notifiable, Notification $notification)
    {
        $method = 'toWhatsApp';

        return method_exists($notification, method: $method)
            ? $notification->{$method}($notifiable)
            : (
                method_exists($notification, method: 'toSms')
                ? $notification->toSms($notifiable)
                : null
            );
    }
}
