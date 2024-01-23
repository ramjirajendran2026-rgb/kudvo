<?php

namespace App\Notifications\Concerns;

use App\Settings\SmsSettings;

trait HasSmsChannel
{
    protected function getSmsChannel(object $notifiable): ?string
    {
        if (
            ! method_exists($notifiable, method: 'routeNotificationFor')
            || blank($route = $notifiable->routeNotificationFor('sms', $this))
            || ! phone($route)->isValid()
        ) {
            return null;
        }

        $smsSettings = app(abstract: SmsSettings::class);

        return collect($smsSettings->country_channel)->firstWhere(key: 'country', value: str(phone($route)->getCountry())->upper()->toString())['channel'] ??
            $smsSettings->default_channel;
    }
}
