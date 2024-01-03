<?php

namespace App\Filament\Resources\NominationResource\Widgets;

use App\Filament\Resources\NominationResource\Pages\Electors;
use App\Filament\Resources\NominationResource\Pages\Nominees;
use App\Filament\Resources\NominationResource\Pages\Positions;
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
                ->url(url: Electors::getUrl(parameters: [$this->nomination])),

            Stat::make(label: 'Positions', value: $this->nomination->positions()->count())
                ->url(url: Positions::getUrl(parameters: [$this->nomination])),

            Stat::make(label: 'Nominees', value: 0)
                ->url(url: Nominees::getUrl(parameters: [$this->nomination])),
        ];
    }
}
