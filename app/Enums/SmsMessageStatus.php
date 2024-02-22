<?php

namespace App\Enums;

enum SmsMessageStatus: string
{
    case UNKNOWN = 'unknown';

    case PENDING = 'pending';

    case SENT = 'sent';

    case DELIVERED = 'delivered';

    case FAILED = 'failed';
}
