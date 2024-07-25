<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BallotType: string implements HasColor, HasLabel
{
    case Direct = 'direct';

    case Booth = 'booth';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Direct => 'Direct',
            self::Booth => 'Booth',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Direct => 'primary',
            self::Booth => 'info',
        };
    }
}
