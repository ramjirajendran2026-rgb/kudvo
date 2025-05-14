<?php

namespace App\Settings;

use App\Data\ClicksendConfigData;
use App\Data\FacebookConfigData;
use App\Data\GithubConfigData;
use App\Data\GoogleConfigData;
use App\Data\LinkedInConfigData;
use App\Data\TawkToConfigData;
use App\Data\TwentyFourSevenSmsConfigData;
use App\Data\XConfigData;
use Spatie\LaravelSettings\Settings;

class ServiceConfig extends Settings
{
    public ClicksendConfigData $clicksend;

    public TwentyFourSevenSmsConfigData $twenty_four_seven_sms;

    public TawkToConfigData $tawk_to;

    public FacebookConfigData $facebook;

    public GoogleConfigData $google;

    public GithubConfigData $github;

    public LinkedInConfigData $linkedin;

    public XConfigData $x;

    public static function group(): string
    {
        return 'service_config';
    }
}
