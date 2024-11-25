<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Arr;

enum ElectionResultSortBy: string implements HasLabel
{
    case HighestVotes = 'highest-votes';
    case LowestVotes = 'lowest-votes';

    public static function getOptions(): array
    {
        return Arr::mapWithKeys(self::cases(), fn (self $case): array => [$case->value => $case->getLabel()]);
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HighestVotes => 'Highest Votes',
            self::LowestVotes => 'Lowest Votes',
        };
    }
}
