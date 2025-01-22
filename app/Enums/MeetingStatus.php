<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MeetingStatus: string implements HasColor, HasLabel
{
    case Onboarding = 'onboarding';
    case Published = 'published';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Onboarding => 'Onboarding',
            self::Published => 'Published',
            self::Cancelled => 'Cancelled',
            self::Completed => 'Completed',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Onboarding => 'primary',
            self::Published => 'success',
            self::Cancelled => 'danger',
            self::Completed => 'info',
        };
    }
}
