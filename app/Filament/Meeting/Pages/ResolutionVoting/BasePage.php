<?php

namespace App\Filament\Meeting\Pages\ResolutionVoting;

use App\Filament\Meeting\Pages\Concerns\BelongsToMeeting;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

abstract class BasePage extends Page
{
    use BelongsToMeeting;

    protected static string $view = 'filament.meeting.pages.resolution-voting.base';

    protected bool $preview = false;

    public static function getSlug(): string
    {
        return 'resolution-voting/' . parent::getSlug();
    }

    public function getHeading(): string | Htmlable
    {
        return '';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return null;
    }

    public function isPreview(): bool
    {
        return $this->preview;
    }
}
