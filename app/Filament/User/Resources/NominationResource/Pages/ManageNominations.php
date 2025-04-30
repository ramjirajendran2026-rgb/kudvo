<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Enums\NominationStatus;
use App\Filament\User\Resources\NominationResource;
use Filament\Resources\Pages\ManageRecords;

class ManageNominations extends ManageRecords
{
    protected static string $resource = NominationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            NominationResource::getCreateAction(),
        ];
    }

    public function getTabs(): array
    {
        return NominationStatus::getTabs();
    }
}
