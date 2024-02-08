<?php

namespace App\Filament\Election\Widgets;

use App\Models\Election;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class ElectionStatsOverview extends BaseWidget
{
    public Election $election;

    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
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
            Stat::make(
                label: 'Total Electors',
                value: $total,
            )
                ->color(color: 'info')
                ->icon(icon: 'heroicon-o-user-group'),

            Stat::make(
                label: 'Voted',
                value: $voted.' ('.Number::percentage(number: ($voted / $total) * 100, maxPrecision: 2).')',
            )
                ->color(color: 'success')
                ->icon(icon: 'heroicon-o-face-smile'),

            Stat::make(
                label: 'Non-Voted',
                value: $nonVoted,
            )
                ->color(color: 'warning')
                ->icon(icon: 'heroicon-o-face-frown'),
        ];
    }
}
