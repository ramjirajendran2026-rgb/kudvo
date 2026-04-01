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
use Spatie\LaravelSettings\SettingsCasts\DataCast;
use Spatie\LaravelData\Optional; // Important import

class ServiceConfig extends Settings
{
    public ClicksendConfigData $clicksend;

    // Use |Optional|null for all these properties
    public TwentyFourSevenSmsConfigData|Optional|null $twenty_four_seven_sms;
    public TawkToConfigData|Optional|null $tawk_to;
    public FacebookConfigData|Optional|null $facebook;
    public GoogleConfigData|Optional|null $google;
    public GithubConfigData|Optional|null $github;
    public LinkedInConfigData|Optional|null $linkedin;
    public XConfigData|Optional|null $x;

    public static function group(): string
    {
        return 'service_config';
    }

    public static function casts(): array
    {
        return [
            'twenty_four_seven_sms' => DataCast::class . ':' . TwentyFourSevenSmsConfigData::class,
            'tawk_to' => DataCast::class . ':' . TawkToConfigData::class,
            'facebook' => DataCast::class . ':' . FacebookConfigData::class,
            'google' => DataCast::class . ':' . GoogleConfigData::class,
            'github' => DataCast::class . ':' . GithubConfigData::class,
            'linkedin' => DataCast::class . ':' . LinkedInConfigData::class,
            'x' => DataCast::class . ':' . XConfigData::class,
        ];
    }

    public static function defaults(): array
    {
        return [
            'clicksend' => ['username' => '', 'api_key' => ''],
            'twenty_four_seven_sms' => null,
            'tawk_to' => null,
            'facebook' => null,
            'google' => null,
            'github' => null,
            'linkedin' => null,
            'x' => null,
        ];
    }
}