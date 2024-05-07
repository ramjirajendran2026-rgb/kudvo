<?php

namespace App\Filament\Admin\Clusters\ElectionManagement\Resources\ElectionPlanResource\Pages;

use App\Filament\Admin\Clusters\ElectionManagement\Resources\ElectionPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListElectionPlans extends ListRecords
{
    protected static string $resource = ElectionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
