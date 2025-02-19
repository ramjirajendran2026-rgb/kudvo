<?php

namespace App\Enums;

use App\Models\Meeting;
use App\Models\Participant;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

enum MeetingPanelState: string
{
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case AlreadyVoted = 'already_voted';
    case VotingScheduled = 'voting_scheduled';
    case VotingOpen = 'voting_open';
    case VotingClosed = 'voting_closed';
    case VotingCompleted = 'voting_completed';

    public function getDescription(Meeting $meeting, ?Participant $participant = null): HtmlString | string
    {
        return match ($this) {
            self::Cancelled => 'This meeting has been cancelled. Please contact the organizer for more information.',
            self::Completed => 'This meeting has been completed. Please contact the organizer for more information.',
            self::AlreadyVoted => Markdown::parse(sprintf(
                'Your votes has been submitted at<br />**%s**.',
                $participant->voted_at->timezone($meeting->timezone)->format(format: 'M d, Y h:i A (T)')
            )),
            self::VotingScheduled => new HtmlString(
                html: Blade::render(
                    string: "<x-timer-countdown
                                target='{$meeting->voting_starts_at?->unix()}'
                                reload='true'
                                label='Voting for this meeting will starts in'
                            />"
                )
            ),
            self::VotingOpen => 'Resolution voting for this meeting is open.',
            self::VotingClosed => 'Resolution voting for this meeting is closed. Please contact the organizer for more information.',
            self::VotingCompleted => 'Resolution voting for this meeting has been completed. Please contact the organizer for more information.',
        };
    }

    public function getIcon(Meeting $meeting, ?Participant $participant = null): string
    {
        return match ($this) {
            self::Cancelled => 'heroicon-o-x-circle',
            self::Completed => 'heroicon-o-check-circle',
            self::AlreadyVoted => 'heroicon-o-check-circle',
            self::VotingScheduled => 'heroicon-o-calendar',
            self::VotingOpen => 'heroicon-o-clock',
            self::VotingClosed => 'heroicon-o-stop-circle',
            self::VotingCompleted => 'heroicon-o-check-circle',
        };
    }

    public function getHeading(Meeting $meeting, ?Participant $participant = null): string
    {
        return match ($this) {
            self::Cancelled => 'Cancelled',
            self::Completed => 'Completed',
            self::AlreadyVoted => 'Submitted',
            self::VotingScheduled => 'Voting Scheduled',
            self::VotingOpen => 'Voting Open',
            self::VotingClosed => 'Voting Closed',
            self::VotingCompleted => 'Voting Completed',
        };
    }
}
