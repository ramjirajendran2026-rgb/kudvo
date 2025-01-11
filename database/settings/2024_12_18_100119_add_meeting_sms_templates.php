<?php

use App\Notifications\Election\MfaCodeNotification;
use App\Notifications\Meeting\MeetingInvitationNotification;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add(
            property: 'sms_templates.meeting_invitation',
            value: 'Click on following link to cast your votes for ' . MeetingInvitationNotification::VAR_MEETING_NAME_SHORT . '.' . PHP_EOL .
            PHP_EOL .
            MeetingInvitationNotification::VAR_MEETING_LINK_SHORT
        );

        $this->migrator->add(
            property: 'sms_templates.meeting_participant_mfa',
            value: 'Your OTP verification code is ' . MfaCodeNotification::VAR_CODE . '.' . PHP_EOL .
            PHP_EOL .
            '@' . MfaCodeNotification::VAR_APP_DOMAIN . ' #' . MfaCodeNotification::VAR_CODE,
        );
    }
};
