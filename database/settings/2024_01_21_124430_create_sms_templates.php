<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add(
            property: 'sms_templates.eul',
            value: <<<TEMPLATE
Use the following link to cast your vote for {#ELECTION_NAME#}.

{#BALLOT_LINK#}
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
