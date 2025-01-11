<?php

namespace App\Filament\User\Resources\MeetingResource\Pages;

use App\Filament\User\Resources\MeetingResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;

class CreateMeeting extends CreateRecord
{
    protected static string $resource = MeetingResource::class;

    protected static bool $canCreateAnother = false;

    public static string | Alignment $formActionsAlignment = Alignment::End;

    protected function getRedirectUrl(): string
    {
        return MeetingDashboard::getUrl(parameters: [
            'record' => $this->getRecord(),
            ...$this->getRedirectUrlParameters(),
        ]);
    }
}
