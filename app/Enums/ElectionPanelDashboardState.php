<?php

namespace App\Enums;

use App\Models\Election;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

enum ElectionPanelDashboardState: string
{
    case YetToStart = 'yet_to_start';

    case VotedNow = 'voted_now';

    case AlreadyVoted = 'already_voted';

    case Closed = 'closed';

    case Completed = 'completed';

    case Expired = 'expired';

    public function getLabel(Election $election): string
    {
        return match ($this) {
            self::YetToStart => 'Yet to start',
            self::VotedNow => 'Voted successfully',
            self::AlreadyVoted => 'Already voted',
            self::Closed,
            self::Completed => 'Voting closed',
            self::Expired => 'Voting ended',
        };
    }

    public function getIcon(Election $election): string
    {
        return match ($this) {
            self::VotedNow,
            self::AlreadyVoted => 'heroicon-o-check-badge',
            self::YetToStart,
            self::Closed,
            self::Completed,
            self::Expired => 'heroicon-o-clock',
        };
    }

    public function getDescription(Election $election): HtmlString|string
    {
        return match ($this) {
            self::YetToStart => new HtmlString(
                html: Blade::render(
                    string: '<x-timer-countdown target="'.$election->starts_at->unix().'"'.
                    ' label="Voting for this election will starts in "'.
                    ' reload="true" />'
                )
            ),
            self::VotedNow => 'You vote has been submitted successfully.',
            self::AlreadyVoted => 'You have already casted your vote for this election.',
            self::Closed,
            self::Completed => 'Voting for this election is closed',
            self::Expired => 'Voting for this election is ended',
        };
    }
}
