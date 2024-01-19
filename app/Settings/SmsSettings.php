<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SmsSettings extends Settings
{
    public array $twenty_four_seven_sms;

    public static function group(): string
    {
        return 'sms';
    }
}
