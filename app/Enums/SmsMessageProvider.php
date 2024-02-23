<?php

namespace App\Enums;

use App\Services\Clicksend\ClicksendChannel;
use App\Services\TwentyFourSevenSms\TwentyFourSevenSmsChannel;

enum SmsMessageProvider: string
{
    case CLICKSEND = ClicksendChannel::NAME;
    case TWENTY_FOUR_SEVEN_SMS = TwentyFourSevenSmsChannel::NAME;
}
