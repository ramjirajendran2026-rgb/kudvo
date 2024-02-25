<?php

namespace App\Filament\User\Resources\ElectionResource\Widgets;

use App\Enums\BallotType;
use App\Models\Election;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class ElectionStatsOverview extends BaseWidget
{
    public Election $election;

    protected function getPollingInterval(): ?string
    {
        return $this->election->is_open ? '10s' : null;
    }

    protected function getStats(): array
    {
        $total = $this->election->electors()->count();
        $voted = $this->election->electors()
            ->whereHas(
                relation: 'ballot',
                callback: fn (Builder $query) => $query->where('type', BallotType::Direct->value)->scopes(scopes: 'voted')
            )
            ->count();
        $boothVoted = $this->election->electors()
            ->whereHas(
                relation: 'ballot',
                callback: fn (Builder $query) => $query->where('type', BallotType::Booth->value)->scopes(scopes: 'voted')
            )
            ->count();
        $nonVoted = $total - $voted - $boothVoted;

        return [
            Stat::make(
                label: 'Total Electors',
                value: $total,
            )
                ->color(color: 'info')
                ->extraAttributes(attributes: ['class' => '[&_div.text-3xl]:text-info-600 [&_div.text-3xl]:dark:text-info-400'])
                ->icon(icon: 'heroicon-o-user-group'),

            Stat::make(
                label: 'Voted (Online)',
                value: $voted.' ('.Number::percentage(number: ($voted / $total) * 100, maxPrecision: 2).')',
            )
                ->color(color: 'success')
                ->extraAttributes(attributes: ['class' => '[&_div.text-3xl]:text-success-600 [&_div.text-3xl]:dark:text-success-400'])
                ->icon(icon: 'heroicon-o-face-smile'),

            Stat::make(
                label: 'Voted (Booth)',
                value: $boothVoted.' ('.Number::percentage(number: ($boothVoted / $total) * 100, maxPrecision: 2).')',
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

