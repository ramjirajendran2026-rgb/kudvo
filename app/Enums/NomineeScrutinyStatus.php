<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum NomineeScrutinyStatus: string
    implements HasIcon, HasLabel
{
    case PENDING = 'pending';

    case APPROVED = 'approved';

    case REJECTED = 'rejected';

    public function getLabel(): ?string
    {
        return Str::title(value: $this->value);
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-m-question-mark-circle',
            self::APPROVED => 'heroicon-m-hand-thumb-up',
            self::REJECTED => 'heroicon-m-hand-thumb-down',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
