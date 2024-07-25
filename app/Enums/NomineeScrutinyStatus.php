<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

enum NomineeScrutinyStatus: string implements HasColor, HasIcon, HasLabel
{
    case PENDING = 'pending';

    case APPROVED = 'approved';

    case REJECTED = 'rejected';

    public static function options(): array
    {
        return Arr::mapWithKeys(
            array: self::cases(),
            callback: fn (self $case): array => [$case->value => $case->getLabel()],
        );
    }

    public function getLabel(): ?string
    {
        return Str::title(value: $this->value);
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-m-question-mark-circle',
            self::APPROVED => 'heroicon-m-check',
            self::REJECTED => 'heroicon-m-x-mark',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
