<?php

namespace App\Enums;

enum WhatsAppMessageStatus: string
{
    case Unknown = 'unknown';
    case Pending = 'pending';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Read = 'read';
    case Failed = 'failed';
}
