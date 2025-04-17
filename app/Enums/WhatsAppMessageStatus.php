<?php

namespace App\Enums;

enum WhatsAppMessageStatus: string
{
    case UNKNOWN = 'unknown';
    case PENDING = 'pending';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case READ = 'read';
    case FAILED = 'failed';
}
