<?php

namespace App\Filament\Admin\Clusters\ElectionManagement\Resources\ElectionPriceResource\Pages;

use App\Filament\Admin\Clusters\ElectionManagement\Resources\ElectionPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageElectionPrices extends ManageRecords
{
    protected static string $resource = ElectionPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
