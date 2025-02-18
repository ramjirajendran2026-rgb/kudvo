<?php

namespace App\Filament\User\Resources\MeetingResource\Widgets;

use App\Filament\User\Resources\MeetingResource\Pages\MeetingParticipants;
use App\Filament\User\Resources\MeetingResource\Pages\MeetingResolutions;
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
        return 4;
    }

    protected function getStats(): array
    {
        $meeting = $this->getMeeting()
            ->loadCount(['resolutions', 'participants', 'votedParticipants', 'nonVotedParticipants']);

        return [
            BaseWidget\Stat::make(label: 'Resolutions', value: $meeting->resolutions_count)
                ->icon(icon: MeetingResolutions::getNavigationIcon()),

            BaseWidget\Stat::make(label: 'Participants', value: $meeting->participants_count)
                ->icon(icon: MeetingParticipants::getNavigationIcon()),

            BaseWidget\Stat::make(label: 'Voted', value: $meeting->voted_participants_count)
                ->icon(icon: 'heroicon-o-check-circle'),

            BaseWidget\Stat::make(label: 'Non-voted', value: $meeting->non_voted_participants_count)
                ->icon(icon: 'heroicon-o-x-circle'),
        ];
    }
}
