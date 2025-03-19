<?php

use App\Notifications\Survey\AcknowledgementNotification;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add(
            property: 'sms_templates.survey_acknowledgement',
            value: 'Thank you for registering. Your reference number is  ' . AcknowledgementNotification::VAR_REFERENCE_NUMBER . '.'
        );
    }
};
