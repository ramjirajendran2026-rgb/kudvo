<?php

namespace App\Filament\User\Resources\MeetingResource\Widgets;

use App\Models\Meeting;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class VotersTurnoutChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected static ?string $maxHeight = '300px';

    public Meeting $record;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Voters Turnout',
                    'data' => [
                        $this->getMeeting()->votedParticipants()->count(),
                        $this->getMeeting()->nonVotedParticipants()->count(),
                    ],
                ],
            ],
            'labels' => [
                'Voted',
                'Non-voted',
            ],
        ];
    }

    protected function getOptions(): array | RawJs | null
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'indexAxis' => 'y',
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => true,
                    ],
                    'ticks' => [
                        'display' => true,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => true,
                    ],
                    'ticks' => [
                        'display' => true,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getHeading(): string | Htmlable | null
    {
        return null;
    }

    protected function getMeeting(): Meeting
    {
        return $this->record;
    }
}
