<?php

namespace App\Filament\Admin\Clusters\SmsSettingsCluster\Resources\SmsPriceResource\Pages;

use App\Filament\Admin\Clusters\SmsSettingsCluster\Resources\SmsPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSmsPrices extends ManageRecords
{
    protected static string $resource = SmsPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalCancelAction(false),
        ];
    }
}
