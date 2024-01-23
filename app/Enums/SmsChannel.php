<?php

namespace App\Enums;

use App\Services\TwentyFourSevenSms\TwentyFourSevenSmsChannel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Arr;

enum SmsChannel: string implements HasLabel, HasColor
{
    case Clicksend = 'Clicksend';

    case TwentyFourSevenSms = TwentyFourSevenSmsChannel::NAME;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Clicksend => 'Clicksend',
            self::TwentyFourSevenSms => '24x7 SMS',
        };
    }

    public function getColor(): string|array|null
    {
        return 'info';
    }
}
