<?php

namespace App\Filament\Meeting\Pages;

use App\Facades\Kudvo;
use App\Models\Meeting;
use App\Models\Participant;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

abstract class BasePage extends Page
{
    public Meeting $meeting;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        $parameters['meeting'] ??= Kudvo::getMeeting()?->getRouteKey();

        return parent::getUrl($parameters, $isAbsolute, $panel, $tenant);
    }

    public function mount(): void
    {
        $this->meeting = Kudvo::getMeeting();
    }

    public function getMeeting(): Meeting
    {
        return $this->meeting;
    }

    public function getParticipant(): Participant
    {
        /** @var Participant $participant */
        $participant = filament()->auth()->user();

        return $participant;
    }
}
