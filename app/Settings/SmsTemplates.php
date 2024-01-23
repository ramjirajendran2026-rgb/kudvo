<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SmsTemplates extends Settings
{
    public string $elector_ballot_link;

    public string $elector_ballot_mfa;

    public string $elector_nomination_mfa;

    public static function group(): string
    {
        return 'sms_templates';
    }
}
