<?php

namespace App\Filament\User\Resources\MeetingResource\Pages;

use App\Filament\User\Resources\MeetingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMeetings extends ManageRecords
{
    protected static string $resource = MeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
