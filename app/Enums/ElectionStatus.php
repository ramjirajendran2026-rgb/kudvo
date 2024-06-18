<?php

namespace App\Enums;

use Filament\Resources\Components\Tab;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

enum ElectionStatus: string implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 'draft';

    case PUBLISHED = 'published';

    case OPEN = 'open';

    case COMPLETED = 'completed';

    case CANCELLED = 'cancelled';

    case CLOSED = 'closed';

    public function getScopes(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'draft',
            self::PUBLISHED => 'published',
            self::OPEN => 'open',
            self::COMPLETED => 'completed',
            default => null,
        };
    }

    public static function getTabs(): array
    {
        return [
            'all' => Tab::make(label: __(key: 'app.all')),

            ...collect(self::cases())
                ->filter(fn (self $case) => filled($case->getScopes()))
                ->mapWithKeys(fn (self $case) => $case->getTab())
                ->toArray(),
        ];
    }

    public function getTab(): array
    {
        return [
            $this->value => Tab::make(label: $this->getLabel())
                ->icon(icon: $this->getIcon())
                ->modifyQueryUsing(
                    callback: fn (Builder $query): Builder => $query->scopes(scopes: $this->getScopes())
                ),
        ];
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'info',
            self::PUBLISHED => 'primary',
            self::OPEN => 'success',
            self::COMPLETED, self::CLOSED, self::CANCELLED => 'warning',
        };
    }

    public function getLabel(): ?string
    {
        return __(key: 'app.enums.election_status.'.$this->value.'.label');
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-m-pencil',
            self::PUBLISHED => 'heroicon-m-megaphone',
            self::OPEN => 'heroicon-m-clock',
            self::COMPLETED, self::CLOSED, self::CANCELLED => 'heroicon-m-document-check',
        };
    }
}
