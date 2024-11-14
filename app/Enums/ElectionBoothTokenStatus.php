<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ElectionBoothTokenStatus: string implements HasColor, HasLabel
{
    case PendingActivation = 'pending_activation';
    case Occupied = 'occupied';
    case Available = 'available';

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PendingActivation => 'info',
            self::Occupied => 'danger',
            self::Available => 'success',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PendingActivation => 'Pending Activation',
            self::Occupied => 'Occupied',
            self::Available => 'Available',
        };
    }
}
