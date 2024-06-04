<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BallotLinkBlastStatus: string implements HasLabel
{
    case Scheduled = 'scheduled';
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::Pending => 'Pending',
            self::Running => 'Running',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}
