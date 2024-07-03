<?php

namespace App\Filament\Admin\Resources\ElectionPlanResource\Pages;

use App\Filament\Admin\Resources\ElectionPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListElectionPlans extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = ElectionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
