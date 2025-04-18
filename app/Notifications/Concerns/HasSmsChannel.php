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

    protected function prepareSmsChannel(object $notifiable, array $via): array
    {
        return collect($via)
            ->when(
                value: in_array(needle: 'sms', haystack: $via),
                callback: fn ($collection) => $collection
                    ->reject(callback: fn ($channel) => $channel === 'sms')
                    ->merge($this->getSmsChannel(notifiable: $notifiable) ?? []),
                default: fn ($collection) => $collection
            )
            ->toArray();
    }
}
