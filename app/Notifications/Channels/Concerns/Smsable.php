<?php

namespace App\Notifications\Channels\Concerns;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

trait Smsable
{
    protected function getSmsRoute(object $notifiable, Notification $notification, string $channel = 'sms'): ?string
    {
        if (method_exists($notifiable, method: 'routeNotificationFor')) {
            return $notifiable->routeNotificationFor($channel, $notification)
                ?: $notifiable->routeNotificationFor('sms', $notification);
        }

        return null;
    }

    protected function getSmsMessage(object $notifiable, Notification $notification, string $channel = 'sms')
    {
        $method = 'to' . Str::studly($channel);

        return method_exists($notification, method: $method)
            ? $notification->{$method}($notifiable)
            : (
                method_exists($notification, method: 'toSms')
                ? $notification->toSms($notifiable)
                : null
            );
    }
}
