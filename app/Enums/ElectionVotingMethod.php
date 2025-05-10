<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Arr;

enum ElectionVotingMethod: string implements HasLabel
{
    case Standard = 'standard';
    case Distributed = 'distributed';

    public static function getOptions(): array
    {
        return Arr::mapWithKeys(self::cases(), fn (self $case): array => [$case->value => $case->getLabel()]);
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Standard => 'Standard',
            self::Distributed => 'Distributed',
        };
    }

    public function getVotesPickerView(): string
    {
        return match ($this) {
            self::Standard => 'forms.components.votes-picker.standard',
            self::Distributed => 'forms.components.votes-picker.distributed',
        };
    }

    public function canHavePositionQuota(): bool
    {
        return match ($this) {
            self::Standard => true,
            self::Distributed => false,
        };
    }
}
