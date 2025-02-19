<?php

namespace App\Actions\Meeting;

use App\Enums\MeetingPanelState;
use App\Enums\MeetingStatus;
use App\Enums\MeetingVotingStatus;
use App\Facades\Kudvo;
use App\Models\Meeting;
use App\Models\Participant;

class DetectMeetingPanelState
{
    public function execute(Meeting $meeting, ?Participant $participant = null): void
    {
        Kudvo::setMeetingPanelState(match (true) {
            $meeting->isStatus(MeetingStatus::Cancelled) => MeetingPanelState::Cancelled,
            $participant?->is_voted => MeetingPanelState::AlreadyVoted,
            $meeting->isStatus(MeetingStatus::Completed),
            $meeting->isVotingStatus(MeetingVotingStatus::Closed),
            $meeting->isVotingStatus(MeetingVotingStatus::Ended) => MeetingPanelState::VotingClosed,
            $meeting->isVotingStatus(MeetingVotingStatus::Open) => MeetingPanelState::VotingOpen,
            $meeting->isVotingStatus(MeetingVotingStatus::Scheduled) => MeetingPanelState::VotingScheduled,
            default => null,
        });
    }
}
