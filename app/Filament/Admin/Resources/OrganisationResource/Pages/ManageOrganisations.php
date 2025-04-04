<?php

namespace App\Filament\Admin\Resources\OrganisationResource\Pages;

use App\Filament\Admin\Resources\OrganisationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOrganisations extends ManageRecords
{
    use ManageRecords\Concerns\Translatable;

    protected static string $resource = OrganisationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
