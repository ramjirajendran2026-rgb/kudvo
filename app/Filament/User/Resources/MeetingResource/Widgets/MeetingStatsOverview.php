<?php

namespace App\Filament\User\Resources\MeetingResource\Widgets;

use App\Models\Meeting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class MeetingStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public Meeting $record;

    public function getMeeting(): Meeting
    {
        return $this->record;
    }

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $meeting = $this->getMeeting()
            ->loadCount(['resolutions', 'participants', 'votedParticipants', 'nonVotedParticipants']);

        return [
            BaseWidget\Stat::make(label: 'Total Resolutions', value: $meeting->resolutions_count),
            BaseWidget\Stat::make(label: 'Total Participants', value: $meeting->participants_count),
            BaseWidget\Stat::make(label: 'Voting Completed', value: $meeting->voted_participants_count),
            BaseWidget\Stat::make(label: 'Voting Pending', value: $meeting->non_voted_participants_count),
        ];
    }
}
