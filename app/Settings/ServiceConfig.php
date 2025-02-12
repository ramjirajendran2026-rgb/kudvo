<?php

namespace App\Settings;

use App\Data\ClicksendConfigData;
use App\Data\TawkToConfigData;
use App\Data\TwentyFourSevenSmsConfigData;
use Spatie\LaravelSettings\Settings;

class ServiceConfig extends Settings
{
    public ClicksendConfigData $clicksend;

    public TwentyFourSevenSmsConfigData $twenty_four_seven_sms;

    public TawkToConfigData $tawk_to;

    public static function group(): string
    {
        return 'service_config';
    }
}
