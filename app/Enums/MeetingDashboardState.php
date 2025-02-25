<?php

namespace App\Enums;

use App\Filament\User\Resources\MeetingResource\Pages\MeetingParticipants;
use App\Filament\User\Resources\MeetingResource\Pages\MeetingResolutions;
use App\Models\Meeting;
use Filament\Support\Markdown;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

enum MeetingDashboardState: string
{
    case OnboardParticipants = 'onboard_participants';
    case OnboardResolutions = 'onboard_resolutions';
    case ReadyToPublish = 'ready_to_publish';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case VotingScheduled = 'voting_scheduled';
    case VotingInProgress = 'voting_in_progress';
    case VotingEnded = 'voting_ended';
    case VotingClosed = 'voting_closed';

    public function getDescription(Meeting $meeting): HtmlString | string | null
    {
        return match ($this) {
            self::VotingScheduled => new HtmlString(
                html: Blade::render(
                    string: '<x-timer-countdown wire:key="open-timer-' . $meeting->voting_starts_at->unix() . '" target="' . $meeting->voting_starts_at->unix() . '"' .
                    ' label="Resolution voting for this meeting will starts in "' .
                    ' reload="true" />'
                )
            ),
            self::VotingInProgress => new HtmlString(
                html: Blade::render(
                    string: '<x-timer-countdown wire:key="close-timer-' . $meeting->voting_ends_at->unix() . '" target="' . $meeting->voting_ends_at->unix() . '"' .
                    ' label="Resolution voting for this meeting will ends in "' .
                    ' reload="true" />'
                )
            ),
            self::Cancelled => new HtmlString(
                Markdown::inline('This meeting has been cancelled at **' . $meeting->cancelled_at->timezone($meeting->timezone)->format(format: 'M d, Y h:i A (T)') . '**')
            ),
            self::VotingEnded => new HtmlString(
                Markdown::inline('Resolution voting for this meeting has been ended at<br />**' . $meeting->voting_ends_at_local->format(format: 'M d, Y h:i A (T)') . '**')
            ),
            self::VotingClosed => new HtmlString(
                Markdown::inline('Resolution voting for this meeting has been closed at<br />**' . $meeting->voting_closed_at->timezone($meeting->timezone)->format(format: 'M d, Y h:i A (T)') . '**')
            ),
            default => null,
        };
    }

    public function getIcon(Meeting $meeting): ?string
    {
        return match ($this) {
            self::OnboardParticipants => MeetingParticipants::getNavigationIcon(),
            self::OnboardResolutions => MeetingResolutions::getNavigationIcon(),
            self::ReadyToPublish => $meeting->isCheckoutRequired() ? 'heroicon-o-credit-card' : 'heroicon-o-rocket-launch',
            self::Cancelled => 'heroicon-o-x-circle',
            self::Completed => 'heroicon-o-check-circle',
            self::VotingScheduled => 'heroicon-o-calendar',
            self::VotingInProgress => 'heroicon-o-clock',
            self::VotingEnded => 'heroicon-o-stop-circle',
            self::VotingClosed => 'heroicon-o-check-circle',
        };
    }

    public function getLabel(Meeting $meeting): ?string
    {
        return match ($this) {
            self::OnboardParticipants => 'Add Participants',
            self::OnboardResolutions => 'Add Resolutions',
            self::ReadyToPublish => $meeting->isCheckoutRequired() ? 'Pay to Publish' : 'Ready to Publish',
            self::Cancelled => 'Cancelled',
            self::Completed => 'Completed',
            self::VotingScheduled => 'Voting Scheduled',
            self::VotingInProgress => 'Voting In Progress',
            self::VotingEnded => 'Voting Ended',
            self::VotingClosed => 'Voting Closed',
        };
    }
}
