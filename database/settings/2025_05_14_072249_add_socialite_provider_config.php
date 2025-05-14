<?php

use App\Data\FacebookConfigData;
use App\Data\GithubConfigData;
use App\Data\GoogleConfigData;
use App\Data\LinkedInConfigData;
use App\Data\XConfigData;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('service_config.facebook', FacebookConfigData::empty());
        $this->migrator->add('service_config.google', GoogleConfigData::empty());
        $this->migrator->add('service_config.github', GithubConfigData::empty());
        $this->migrator->add('service_config.linkedin', LinkedInConfigData::empty());
        $this->migrator->add('service_config.x', XConfigData::empty());
    }
};
