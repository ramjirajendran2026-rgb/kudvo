<?php

namespace App\Filament\Base\Widgets;

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
        $electorsCount = $this->election->electors()->count();

        $votedElectorsCount = $this->election->electors()
            ->whereHas(
                relation: 'ballot',
                callback: fn (Builder $query) => $query->scopes(scopes: ['live', 'voted'])
            )
            ->count();

        $nonVotedElectorsCount = $electorsCount - $votedElectorsCount;

        return [
            Stat::make(
                label: __('filament.base.widgets.election_stats_overview.total_electors.label'),
                value: $electorsCount,
            )
                ->color(color: 'info')
                ->extraAttributes(attributes: ['class' => '[&_div.text-3xl]:text-info-600 [&_div.text-3xl]:dark:text-info-400'])
                ->icon(icon: 'heroicon-o-user-group'),

            Stat::make(
                label: __('filament.base.widgets.election_stats_overview.voted_electors.label'),
                value: $votedElectorsCount . ' (' . Number::percentage(number: ($votedElectorsCount / $electorsCount) * 100, maxPrecision: 2) . ')',
            )
                ->color(color: 'success')
                ->extraAttributes(attributes: ['class' => '[&_div.text-3xl]:text-success-600 [&_div.text-3xl]:dark:text-success-400'])
                ->icon(icon: 'heroicon-o-face-smile'),

            Stat::make(
                label: __('filament.base.widgets.election_stats_overview.non_voted_electors.label'),
                value: $nonVotedElectorsCount,
            )
                ->color(color: 'warning')
                ->extraAttributes(attributes: ['class' => '[&_div.text-3xl]:text-warning-600 [&_div.text-3xl]:dark:text-warning-400'])
                ->icon(icon: 'heroicon-o-face-frown'),
        ];
    }
}
