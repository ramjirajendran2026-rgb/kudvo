<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionStatus;
use App\Filament\User\Resources\ElectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageElections extends ManageRecords
{
    protected static string $resource = ElectionResource::class;

    protected ?string $heading = '';

    public function getTabs(): array
    {
        return ElectionStatus::getTabs();
    }
}
