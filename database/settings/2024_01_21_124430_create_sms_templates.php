<?php

use App\Notifications\ElectorBallotLinkNotification;
use App\Notifications\ElectionMfaNotification;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add(
            property: 'sms_templates.elector_ballot_link',
            value: 'Click on following link to cast your votes for '.ElectorBallotLinkNotification::VAR_ELECTION_NAME_SHORT.'.'.PHP_EOL.
                PHP_EOL.
                ElectorBallotLinkNotification::VAR_BALLOT_LINK_SHORT
        );

        $this->migrator->add(
            property: 'sms_templates.elector_ballot_mfa',
            value: 'Your OTP verification code is '.ElectionMfaNotification::VAR_CODE.'.'.PHP_EOL.
                PHP_EOL.
                '@'.ElectionMfaNotification::VAR_APP_DOMAIN.' #'.ElectionMfaNotification::VAR_CODE,
        );

        $this->migrator->add(
            property: 'sms_templates.elector_nomination_mfa',
            value: 'Your OTP verification code is '.ElectionMfaNotification::VAR_CODE.'.'.PHP_EOL.
            PHP_EOL.
            '@'.ElectionMfaNotification::VAR_APP_DOMAIN.' #'.ElectionMfaNotification::VAR_CODE,
        );
    }
};
