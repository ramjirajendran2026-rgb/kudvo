<?php

namespace App\Enums;

use App\Services\TwentyFourSevenSms\TwentyFourSevenSmsChannel;

enum SmsMessageProvider: string
{
    case TWENTY_FOUR_SEVEN_SMS = TwentyFourSevenSmsChannel::NAME;
}
