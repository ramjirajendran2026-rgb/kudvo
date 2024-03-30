<?php

namespace App\Enums;

use Filament\Resources\Components\Tab;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

enum MailMessagePurpose: string implements HasLabel
{
    case BallotLink = 'ballot_link';

    case BallotMfaCode = 'ballot_mfa_code';

    case VotedConfirmation = 'voted_confirmation';

    case VotedBallotCopy = 'voted_ballot_copy';

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }

    public static function getTabs(): array
    {
        return Arr::mapWithKeys(
            array: self::cases(),
            callback: fn (self $case) => [
                $case->value => Tab::make(label: $case->getLabel())
                    ->modifyQueryUsing(callback: fn (Builder $query) => $case->getTabQuery($query)),
            ],
        );
    }

    public function getTabQuery(Builder $query): Builder
    {
        return match ($this) {
            self::BallotLink => $query->where('purpose', self::BallotLink),
            self::BallotMfaCode => $query->where('purpose', self::BallotMfaCode),
            self::VotedConfirmation => $query->where('purpose', self::VotedConfirmation),
            self::VotedBallotCopy => $query->where('purpose', self::VotedBallotCopy),
        };
    }
}
