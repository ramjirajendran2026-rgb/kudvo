<?php

namespace App\Enums;

use App\Models\Election;
use App\Models\Elector;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

enum ElectionPanelState: string
{
    case YetToStart = 'yet_to_start';

    case Open = 'open';

    case Closed = 'closed';

    case Voted = 'voted';

    case Cancelled = 'cancelled';

    case DeviceAlreadyUsed = 'device_already_used';

    case DeviceNotSupported = 'device_not_supported';

    case UniqueLinkRequired = 'unique_link_required';

    public function getLabel(Election $election): HtmlString|string|null
    {
        return match ($this) {
            self::YetToStart => 'Yet to start',
            self::Closed => 'Closed',
            self::Voted => 'Submitted',
            self::Cancelled => 'Cancelled',
            self::DeviceAlreadyUsed => 'Restricted device',
            self::DeviceNotSupported => 'Unsupported device',
            self::UniqueLinkRequired => 'Unique link required',
            default => null,
        };
    }

    public function getIcon(Election $election): ?string
    {
        return match ($this) {
            self::YetToStart,
            self::Closed => 'heroicon-o-clock',
            self::Voted => 'heroicon-o-shield-check',
            self::Cancelled => 'heroicon-o-x-mark',
            self::DeviceAlreadyUsed => 'heroicon-o-flag',
            self::DeviceNotSupported,
            self::UniqueLinkRequired => 'heroicon-o-no-symbol',
            default => null,
        };
    }

    public function getDescription(Election $election, ?Elector $elector = null): string | HtmlString |null
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
            self::Closed => 'Voting for this election was closed.',
            self::Voted => 'You have submitted your votes.',
            self::Cancelled => 'This election has been cancelled. Please contact Election Officer(s) for more information.',
            self::DeviceAlreadyUsed => 'This device is already used to cast vote for this election.',
            self::DeviceNotSupported => 'This device is not supported for this election. Please use any of Chrome (Android) and Safari (iOS)',
            self::UniqueLinkRequired => 'You need to use unique link to cast vote for this election. You might received a unique link in your email or SMS. Please use that link to cast vote.',
            default => null,
        };
    }
}
