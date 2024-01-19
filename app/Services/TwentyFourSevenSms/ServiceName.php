<?php

namespace App\Services\TwentyFourSevenSms;

enum ServiceName
{
    case TEMPLATE_BASED;

    case PROMOTIONAL_HIGH;

    case PROMOTIONAL_SPL;

    case OPTIN_OPTOUT;

    case INTERNATIONAL;
}
