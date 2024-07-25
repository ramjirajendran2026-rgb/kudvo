<?php

use App\Notifications\Election\BallotLinkNotification;
use App\Notifications\Election\MfaCodeNotification;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add(
            property: 'sms_templates.elector_ballot_link',
            value: 'Click on following link to cast your votes for ' . BallotLinkNotification::VAR_ELECTION_NAME_SHORT . '.' . PHP_EOL .
                PHP_EOL .
                BallotLinkNotification::VAR_BALLOT_LINK_SHORT
        );

        $this->migrator->add(
            property: 'sms_templates.elector_ballot_mfa',
            value: 'Your OTP verification code is ' . MfaCodeNotification::VAR_CODE . '.' . PHP_EOL .
                PHP_EOL .
                '@' . MfaCodeNotification::VAR_APP_DOMAIN . ' #' . MfaCodeNotification::VAR_CODE,
        );

        $this->migrator->add(
            property: 'sms_templates.elector_nomination_mfa',
            value: 'Your OTP verification code is ' . MfaCodeNotification::VAR_CODE . '.' . PHP_EOL .
            PHP_EOL .
            '@' . MfaCodeNotification::VAR_APP_DOMAIN . ' #' . MfaCodeNotification::VAR_CODE,
        );

        $this->migrator->add(
            property: 'sms_templates.elector_voted_confirmation',
            value: 'Thank you for voting. Your vote has been successfully casted.'
        );
    }
};
