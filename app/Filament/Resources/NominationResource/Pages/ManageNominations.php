<?php

namespace App\Filament\Resources\NominationResource\Pages;

use App\Filament\Resources\NominationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNominations extends ManageRecords
{
    protected static string $resource = NominationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
