<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ElectionBoothTokenStatus: string implements HasLabel
{
    case PendingActivation = 'pending_activation';
    case Occupied = 'occupied';
    case Available = 'available';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PendingActivation => 'Pending Activation',
            self::Occupied => 'Occupied',
            self::Available => 'Available',
        };
    }
}
