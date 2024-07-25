<?php

namespace App\Enums;

use App\Models\Election;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

enum ElectionDashboardState: string
{
    case PendingPreference = 'pending_preference';

    case PendingElectorsList = 'pending_electors_list';

    case PendingBallotSetup = 'pending_ballot';

    case PendingTiming = 'pending_timing';

    case PendingCheckout = 'pending_checkout';

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
            self::PendingPreference => __('app.enums.election_dashboard_state.pending_preference.label'),
            self::PendingElectorsList => __('app.enums.election_dashboard_state.pending_electors_list.label'),
            self::PendingBallotSetup => __('app.enums.election_dashboard_state.pending_ballot.label'),
            self::PendingTiming => __('app.enums.election_dashboard_state.pending_timing.label'),
            self::PendingCheckout => __('app.enums.election_dashboard_state.pending_checkout.label'),
            self::ReadyToPublish => __('app.enums.election_dashboard_state.draft.label'),
            self::Upcoming => __('app.enums.election_dashboard_state.upcoming.label'),
            self::Open => __('app.enums.election_dashboard_state.open.label'),
            self::Expired => __('app.enums.election_dashboard_state.expired.label'),
            self::Closed => __('app.enums.election_dashboard_state.closed.label'),
            self::Completed => __('app.enums.election_dashboard_state.completed.label'),
            self::Cancelled => __('app.enums.election_dashboard_state.cancelled.label'),
        };
    }

    public function getIcon(Election $election): string
    {
        return match ($this) {
            self::PendingPreference => 'heroicon-o-cog',
            self::PendingElectorsList => 'heroicon-o-user-plus',
            self::PendingBallotSetup => 'heroicon-o-document-text',
            self::PendingTiming, self::Upcoming, self::Expired => 'heroicon-o-clock',
            self::PendingCheckout => 'heroicon-o-credit-card',
            self::ReadyToPublish => 'heroicon-o-check-circle',
            self::Open => 'heroicon-o-archive-box',
            self::Closed => 'heroicon-o-archive-box-x-mark',
            self::Completed => 'heroicon-o-check',
            self::Cancelled => 'heroicon-o-x-mark',
        };
    }

    public function getDescription(Election $election): HtmlString | string | null
    {
        return match ($this) {
            self::Upcoming => new HtmlString(
                html: Blade::render(
                    string: '<x-timer-countdown wire:key="open-timer-' . $election->starts_at->unix() . '" target="' . $election->starts_at->unix() . '"' .
                    ' label="Voting for this election will starts in "' .
                    ' reload="true" />'
                )
            ),
            self::Open => new HtmlString(
                html: Blade::render(
                    string: '<x-timer-countdown wire:key="close-timer-' . $election->ends_at->unix() . '" target="' . $election->ends_at->unix() . '"' .
                    ' label="Voting for this election will ends in "' .
                    ' reload="true" />'
                )
            ),
            self::PendingTiming => __('app.enums.election_dashboard_state.pending_timing.description'),
            self::PendingCheckout => __('app.enums.election_dashboard_state.pending_checkout.description'),
            self::Closed => __('app.enums.election_dashboard_state.closed.description', ['datetime' => $election->closed_at->timezone($election->timezone)->format(format: 'M d, Y h:i A (T)')]),
            self::Cancelled => __('app.enums.election_dashboard_state.cancelled.description', ['datetime' => $election->cancelled_at->timezone($election->timezone)->format(format: 'M d, Y h:i A (T)')]),
            default => null,
        };
    }
}
