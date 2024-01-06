<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum NomineeStatus: string
    implements HasColor, HasIcon, HasLabel
{
    case PENDING = 'pending';

    case ACCEPTED = 'accepted';

    case DECLINED = 'declined';

    public function getLabel(): ?string
    {
        return Str::title(value: $this->value);
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-m-question-mark-circle',
            self::ACCEPTED => 'heroicon-m-hand-thumb-up',
            self::DECLINED => 'heroicon-m-hand-thumb-down',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::ACCEPTED => 'success',
            self::DECLINED => 'danger',
        };
    }
}
