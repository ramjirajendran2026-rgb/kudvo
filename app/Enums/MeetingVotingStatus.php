<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MeetingVotingStatus: string implements HasColor, HasLabel
{
    case NotApplicable = 'not_applicable';
    case Scheduled = 'scheduled';
    case Open = 'open';
    case Ended = 'ended';
    case Closed = 'closed';

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::NotApplicable => 'gray',
            self::Scheduled => 'info',
            self::Open => 'primary',
            self::Ended => 'warning',
            self::Closed => 'success',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NotApplicable => 'NA',
            self::Scheduled => 'Scheduled',
            self::Open => 'Open',
            self::Ended => 'Ended',
            self::Closed => 'Closed',
        };
    }
}
