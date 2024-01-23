<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SmsSettings extends Settings
{
    public ?string $default_channel;

    public array $country_channel;

    public static function group(): string
    {
        return 'sms';
    }
}
