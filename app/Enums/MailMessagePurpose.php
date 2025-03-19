<?php

namespace App\Enums;

use App\Models\Election;
use App\Models\Meeting;
use App\Models\Nomination;
use App\Models\Survey;
use Filament\Resources\Components\Tab;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use UnitEnum;

enum MailMessagePurpose: string implements HasLabel
{
    case BallotLink = 'ballot_link';

    case BallotMfaCode = 'ballot_mfa_code';

    case VotedConfirmation = 'voted_confirmation';

    case VotedBallotCopy = 'voted_ballot_copy';

    case ElectionCollaboratorInvitation = 'election_collaborator_invitation';

    case NominationMfaCode = 'nomination_mfa_code';

    case MeetingInvitation = 'meeting_invitation';

    case MeetingMfaCode = 'meeting_mfa_code';

    case SurveyAcknowledgement = 'survey_acknowledgement';

    public function getLabel(): ?string
    {
        return Str::headline($this->value);
    }

    /**
     * @param  array<UnitEnum>|null  $cases
     */
    public static function getTabs(?array $cases = null): array
    {
        return Arr::mapWithKeys(
            array: $cases ?? self::cases(),
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
            self::VotedConfirmation,
            self::VotedBallotCopy,
            self::ElectionCollaboratorInvitation => Election::class,
            self::NominationMfaCode => Nomination::class,
            self::MeetingInvitation,
            self::MeetingMfaCode => Meeting::class,
            self::SurveyAcknowledgement => Survey::class,
        };
    }
}
