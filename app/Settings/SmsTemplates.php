<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SmsTemplates extends Settings
{
    public string $elector_ballot_link;

    public string $elector_ballot_mfa;

    public string $elector_nomination_mfa;

    public string $elector_voted_confirmation;

    public string $meeting_invitation;

    public string $meeting_participant_mfa;

    public string $survey_acknowledgement;

    public static function group(): string
    {
        return 'sms_templates';
    }
}
