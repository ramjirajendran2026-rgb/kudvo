<?php

use App\Data\ClicksendConfigData;
use App\Data\TwentyFourSevenSmsConfigData;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add(property: 'service_config.clicksend', value: ClicksendConfigData::empty());

        $this->migrator->add(property: 'service_config.twenty_four_seven_sms', value: TwentyFourSevenSmsConfigData::empty());
    }
};
