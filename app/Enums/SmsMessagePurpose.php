<?php

namespace App\Enums;

use App\Models\Election;
use App\Models\Meeting;
use App\Models\Nomination;
use Filament\Resources\Components\Tab;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

enum SmsMessagePurpose: string implements HasLabel
{
    case BallotLink = 'ballot_link';

    case BallotMfaCode = 'ballot_mfa_code';

    case VotedConfirmation = 'voted_confirmation';

    case NominationMfaCode = 'nomination_mfa_code';

    case MeetingInvitation = 'meeting_invitation';

    case MeetingMfaCode = 'meeting_mfa_code';

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
        return $query->where('purpose', $this->value);
    }

    public function getEventType(): string
    {
        return match ($this) {
            self::BallotLink,
            self::BallotMfaCode,
            self::VotedConfirmation => Election::class,
            self::NominationMfaCode => Nomination::class,
            self::MeetingInvitation,
            self::MeetingMfaCode => Meeting::class,
        };
    }
}
