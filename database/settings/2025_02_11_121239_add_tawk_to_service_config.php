<?php

use App\Data\TawkToConfigData;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('service_config.tawk_to', TawkToConfigData::empty());
    }
};
