<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add(
            property: 'sms_templates.elector_voted_confirmation',
            value: 'Thank you for voting. Your vote has been successfully casted.'
        );
    }
};
