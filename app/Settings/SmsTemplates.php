<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SmsTemplates extends Settings
{
    public string $eul;

    public string $otp;

    public static function group(): string
    {
        return 'sms_templates';
    }
}
