<?php

namespace App\Filament\Election\Widgets;

use App\Models\Ballot;
use App\Models\Election;
use Carbon\CarbonPeriod;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;

class ElectionVotingTrends extends ChartWidget
{
    public Election $election;

    protected static ?string $heading = 'Voting Trends';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $total = $this->election->electors()->count();
        $voted = $this->election->electors()
            ->whereHas(
                relation: 'ballot',
                callback: fn (Builder $query) => $query->scopes(scopes: 'voted')
            )
            ->count();
        $nonVoted = $total - $voted;

        return [
            'datasets' => [
                [
                    'label' => 'Votes',
                    'data' => [
                        $voted,
                        $nonVoted,
                    ],
                    'backgroundColor' => [
                        'rgb('.FilamentColor::getColors()['success'][500].')',
                        'rgb('.FilamentColor::getColors()['warning'][500].')',
                    ],
                ],
            ],
            'labels' => [
                'Voted',
                'Non-Voted',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
