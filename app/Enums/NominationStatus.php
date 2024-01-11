<?php

namespace App\Enums;

use Filament\Resources\Components\Tab;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

enum NominationStatus: string
    implements HasColor, HasIcon, HasLabel
{
    case CANCELLED = 'cancelled';

    case CLOSED = 'closed';

    case DRAFT = 'draft';

    case PUBLISHED = 'published';

    case SCRUTINISED = 'scrutinised';

    public function getScopes(): string|array|null
    {
        return match ($this) {
            self::CANCELLED => 'cancelled',
            self::CLOSED => 'closed',
            self::DRAFT => 'draft',
            self::PUBLISHED => 'published',
            self::SCRUTINISED => 'scrutinised',
        };
    }

    public static function getTabs(): array
    {
        return [
            'All' => Tab::make(label: 'All'),

            ...Arr::mapWithKeys(
                array: self::cases(),
                callback: fn(self $case) => $case->getTab()
            )
        ];
    }

    public function getTab(): array
    {
        return [
            $this->getLabel() => Tab::make(label: $this->getLabel())
                ->icon(icon: $this->getIcon())
                ->modifyQueryUsing(
                    callback: fn (Builder $query): Builder => $query->scopes(scopes: Arr::wrap(value: $this->getScopes()))
                )
        ];
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CANCELLED => 'danger',
            self::CLOSED => 'warning',
            self::DRAFT => 'info',
            self::PUBLISHED => 'primary',
            self::SCRUTINISED => 'success',
        };
    }

    public function getLabel(): ?string
    {
        return Str::title(value: $this->value);
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::CANCELLED => 'heroicon-m-bolt-slash',
            self::CLOSED => 'heroicon-m-lock-closed',
            self::DRAFT => 'heroicon-m-pencil',
            self::PUBLISHED => 'heroicon-m-megaphone',
            self::SCRUTINISED => 'heroicon-m-document-check',
        };
    }
}
