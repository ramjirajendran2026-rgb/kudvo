<?php

use App\Notifications\ElectionEulNotification;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $electionName = ElectionEulNotification::VAR_ELECTION_NAME_SHORT;
        $ballotLink = ElectionEulNotification::VAR_BALLOT_LINK_SHORT;

        $this->migrator->add(
            property: 'sms_templates.eul',
            value: <<<TEMPLATE
Use the following link to cast your vote for $electionName.

$ballotLink
TEMPLATE
        );

        $this->migrator->add(
            property: 'sms_templates.otp',
            value: <<<TEMPLATE
Your OTP verification code is {#CODE#}.

@{#APP_DOMAIN#} #{#CODE#}
TEMPLATE
        );
    }
};
