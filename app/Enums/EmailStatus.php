<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum EmailStatus: string implements HasLabel
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Bounced = 'bounced';
    case Complaint = 'complaint';
    case Rejected = 'rejected';
    case RenderingFailed = 'rendering_failed';
    case DeliveryDelayed = 'delivery_delayed';
    case Unknown = 'unknown';

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }
}
