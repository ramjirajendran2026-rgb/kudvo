<?php

namespace App\Filament\Meeting\Pages;

use App\Facades\Kudvo;
use App\Models\Meeting;
use App\Models\Participant;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

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

    public function getSubheading(): string | Htmlable | null
    {

        $startsAt = $this->getMeeting()->voting_starts_at_local;
        $endsAt = $this->getMeeting()->voting_ends_at_local;

        $to = 'to';

        return new HtmlString(
            html: <<<HTML
<span class="flex justify-center items-center gap-4">
<span class="flex flex-col md:flex-row flex-grow justify-center md:justify-end items-end md:gap-2 font-bold">
<span>{$startsAt->format(format: 'M d, Y')}</span>
<span>{$startsAt->format(format: 'h:i A')}</span>
<span>{$startsAt->format(format: '(T)')}</span>
</span>
<span>$to</span>
<span class="flex flex-col md:flex-row flex-grow justify-center md:justify-start items-start md:gap-2 font-bold">
<span>{$endsAt->format(format: 'M d, Y')}</span>
<span>{$endsAt->format(format: 'h:i A')}</span>
<span>{$endsAt->format(format: '(T)')}</span>
</span>
</span>
HTML
        );
    }
}
