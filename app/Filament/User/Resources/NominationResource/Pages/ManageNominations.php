<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Enums\NominationStatus;
use App\Filament\User\Resources\NominationResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;

class ManageNominations extends ManageRecords
{
    protected static string $resource = NominationResource::class;

    protected ?string $heading = '';

    public function getTabs(): array
    {
        return array_merge(
            ['All' => Tab::make(label: 'All')],

            NominationStatus::getTabs(),
        );
    }
}
