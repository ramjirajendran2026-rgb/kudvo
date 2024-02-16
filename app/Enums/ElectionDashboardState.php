<?php

namespace App\Enums;

use App\Filament\ElectionPanel;
use App\Models\Election;
use Filament\Panel;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

enum ElectionDashboardState: string
{
    case PendingPreference = 'pending_preference';

    case PendingElectorsList = 'pending_electors_list';

    case PendingBallotSetup = 'pending_ballot';

    case PendingTiming = 'pending_timing';

    case ReadyToPublish = 'draft';

    case Upcoming = 'upcoming';

    case Open = 'open';

    case Expired = 'expired';

    case Closed = 'closed';

    case Completed = 'completed';

    case Cancelled = 'cancelled';

    public function getLabel(Election $election): string
    {
        return match ($this) {
            self::PendingPreference => 'Configure Preference',
            self::PendingElectorsList => 'Add Electors',
            self::PendingBallotSetup => 'Add Positions and Candidates',
            self::PendingTiming => 'Set Timing',
            self::ReadyToPublish => 'Ready to Publish',
            self::Upcoming => 'Yet to Start',
            self::Open => 'Open for Voting',
            self::Expired => 'Voting time ended',
            self::Closed => 'Voting Closed',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getIcon(Election $election): string
    {
        return match ($this) {
            self::PendingPreference => 'heroicon-o-cog',
            self::PendingElectorsList => 'heroicon-o-user-plus',
            self::PendingBallotSetup => 'heroicon-o-document-text',
            self::PendingTiming, self::Upcoming, self::Expired => 'heroicon-o-clock',
            self::ReadyToPublish => 'heroicon-o-check-circle',
            self::Open => 'heroicon-o-archive-box',
            self::Closed => 'heroicon-o-archive-box-x-mark',
            self::Completed => 'heroicon-o-check',
            self::Cancelled => 'heroicon-o-x-mark',
        };
    }

    public function getDescription(Election $election): HtmlString|string|null
    {
        return match ($this) {
            self::Upcoming => new HtmlString(
                html: Blade::render(
                    string: '<x-timer-countdown wire:key="open-timer-'.$election->starts_at->unix().'" target="'.$election->starts_at->unix().'"'.
                    ' label="Voting for this election will starts in "'.
                    ' reload="true" />'
                )
            ),
            self::Open => new HtmlString(
                html: Blade::render(
                    string: '<x-timer-countdown wire:key="close-timer-'.$election->ends_at->unix().'" target="'.$election->ends_at->unix().'"'.
                    ' label="Voting for this election will ends in "'.
                    ' reload="true" />'
                )
            ),
            self::PendingTiming => 'Set election start and end date and time',
            self::Closed => 'Voting is closed for this election on '.
                $election->closed_at->timezone($election->timezone)->format(format: 'M d, Y h:i A (T)'),
            self::Cancelled => 'This election is cancelled on '.
                $election->cancelled_at->timezone($election->timezone)->format(format: 'M d, Y h:i A (T)'),
            default => null,
        };
    }
}
