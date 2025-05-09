<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Arr;

enum StripeCouponDuration: string implements HasLabel
{
    case Forever = 'forever';
    case Once = 'once';
    case Repeating = 'repeating';

    public static function getOptions(): array
    {
        return Arr::mapWithKeys(self::cases(), fn (self $case): array => [$case->value => $case->getLabel()]);
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Forever => 'Forever',
            self::Once => 'Once',
            self::Repeating => 'Repeating',
        };
    }
}
