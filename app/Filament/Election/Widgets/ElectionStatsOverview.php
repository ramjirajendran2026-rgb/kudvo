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
                ->extraAttributes(attributes: ['class' => '[&_div.text-3xl]:text-info-600 [&_div.text-3xl]:dark:text-info-400'])
                ->icon(icon: 'heroicon-o-user-group'),

            Stat::make(
                label: 'Voted',
                value: $voted.' ('.Number::percentage(number: ($voted / $total) * 100, maxPrecision: 2).')',
            )
                ->color(color: 'success')
                ->extraAttributes(attributes: ['class' => '[&_div.text-3xl]:text-success-600 [&_div.text-3xl]:dark:text-success-400'])
                ->icon(icon: 'heroicon-o-face-smile'),

            Stat::make(
                label: 'Non-Voted',
                value: $nonVoted,
            )
                ->color(color: 'warning')
                ->extraAttributes(attributes: ['class' => '[&_div.text-3xl]:text-warning-600 [&_div.text-3xl]:dark:text-warning-400'])
                ->icon(icon: 'heroicon-o-face-frown'),
        ];
    }
}
