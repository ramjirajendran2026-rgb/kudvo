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
            self::YetToStart => __('app.enums.election_panel_dashboard_state.yet_to_start.label'),
            self::VotedNow => __('app.enums.election_panel_dashboard_state.voted_now.label'),
            self::AlreadyVoted => __('app.enums.election_panel_dashboard_state.already_voted.label'),
            self::Closed => __('app.enums.election_panel_dashboard_state.closed.label'),
            self::Completed => __('app.enums.election_panel_dashboard_state.completed.label'),
            self::Expired => __('app.enums.election_panel_dashboard_state.expired.label'),
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

    public function getDescription(Election $election): HtmlString | string
    {
        return match ($this) {
            self::YetToStart => new HtmlString(
                html: Blade::render(
                    string: '<x-timer-countdown target="' . $election->starts_at->unix() . '"' .
                    ' label="Voting for this election will starts in "' .
                    ' reload="true" />'
                )
            ),
            self::VotedNow => __('app.enums.election_panel_dashboard_state.voted_now.description'),
            self::AlreadyVoted => __('app.enums.election_panel_dashboard_state.already_voted.description'),
            self::Closed => __('app.enums.election_panel_dashboard_state.closed.description'),
            self::Completed => __('app.enums.election_panel_dashboard_state.completed.description'),
            self::Expired => __('app.enums.election_panel_dashboard_state.expired.description'),
        };
    }
}
