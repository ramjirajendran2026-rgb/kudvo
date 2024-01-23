<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add(property: 'sms.default_channel');
        $this->migrator->add(property: 'sms.country_channel', value: []);

        $this->migrator->add(property: 'sms.twenty_four_seven_sms', value: []);
    }
};
