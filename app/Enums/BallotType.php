<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BallotType: string implements HasLabel
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
}
