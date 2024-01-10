<?php

namespace App\Filament\User\Resources\NominationResource\Widgets;

use App\Filament\User\Resources\NominationResource\Pages\Electors;
use App\Filament\User\Resources\NominationResource\Pages\Nominees;
use App\Filament\User\Resources\NominationResource\Pages\Positions;
use App\Models\Nomination;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NominationStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public Nomination $nomination;

    protected function getStats(): array
    {
        return [
            Stat::make(label: 'Electors', value: $this->nomination->electors()->count())
                ->icon(icon: Electors::getActiveNavigationIcon())
                ->url(url: Electors::getUrl(parameters: [$this->nomination])),

            Stat::make(label: 'Positions', value: $this->nomination->positions()->count())
                ->icon(icon: Positions::getActiveNavigationIcon())
                ->url(url: Positions::getUrl(parameters: [$this->nomination])),

            ...$this->nomination->is_draft ?
                [] :
                [
                    Stat::make(label: 'Nominees', value: $this->nomination->nominees()->count())
                        ->icon(icon: Nominees::getActiveNavigationIcon())
                        ->url(url: Nominees::getUrl(parameters: [$this->nomination])),
                ],
        ];
    }

    protected function getColumns(): int
    {
        $count = count($this->getCachedStats());

        if ($count < 3) {
            return $count;
        }

        if (($count % 3) !== 1) {
            return 3;
        }

        return 4;
    }
}
