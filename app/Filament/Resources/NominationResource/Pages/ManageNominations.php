<?php

namespace App\Filament\Resources\NominationResource\Pages;

use App\Enums\NominationStatusEnum;
use App\Filament\Resources\NominationResource;
use Filament\Actions;
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

            NominationStatusEnum::getTabs(),
        );
    }
}
