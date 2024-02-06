<?php

namespace App\Enums;

use App\Models\Election;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

enum ElectionPanelState: string
{
    case YetToStart = 'yet_to_start';

    case Open = 'open';

    case Ended = 'ended';

    case Closed = 'closed';

    case DeviceAlreadyUsed = 'device_already_used';

    case DeviceNotSupported = 'device_not_supported';

    public function getLabel(Election $election): HtmlString|string|null
    {
        return match ($this) {
            self::YetToStart => 'Yet to start',
            self::Ended,
            self::Closed => 'Voting closed',
            self::DeviceAlreadyUsed => 'Restricted device',
            self::DeviceNotSupported => 'Unsupported device',
            default => null,
        };
    }

    public function getIcon(Election $election): ?string
    {
        return match ($this) {
            self::YetToStart,
            self::Ended,
            self::Closed => 'heroicon-o-clock',
            self::DeviceAlreadyUsed => 'heroicon-o-flag',
            self::DeviceNotSupported => 'heroicon-o-no-symbol',
            default => null,
        };
    }

    public function getDescription(Election $election): string | HtmlString |null
    {
        return match ($this) {
            self::YetToStart => new HtmlString(
                html: Blade::render(
                    string: "<x-timer-countdown
                                target='{$election->starts_at?->unix()}'
                                reload='true'
                                label='Voting for this election will starts in'
                            />"
                )
            ),
            self::Ended,
            self::Closed => 'Voting for this election was closed.',
            self::DeviceAlreadyUsed => 'This device is already used to cast vote for this election.',
            self::DeviceNotSupported => 'This device is not supported for this election. Please use any of Chrome (Android) and Safari (iOS)',
            default => null,
        };
    }
}
