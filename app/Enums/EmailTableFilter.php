<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

enum EmailTableFilter: string implements HasLabel
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Bounced = 'bounced';
    case Complained = 'complained';
    case DeliveryDelayed = 'delivery_delayed';
    case Rejected = 'rejected';
    case RenderingFailed = 'rendering_failed';

    public static function getFilters(): array
    {
        return Arr::map(self::cases(), fn (self $case) => $case->getFilter());
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Bounced => 'Bounced',
            self::Complained => 'Complained',
            self::Delivered => 'Delivered',
            self::DeliveryDelayed => 'Delivery Delayed',
            self::Rejected => 'Rejected',
            self::RenderingFailed => 'Rendering Failed',
            self::Sent => 'Sent',
            self::Pending => 'Pending',
        };
    }

    public function getFilter(): Filter
    {
        return Filter::make($this->getFieldName())
            ->default($this->isDefault())
            ->label($this->getLabel())
            ->toggle()
            ->query(
                fn (Builder $query) => $query
                    ->when(
                        $this === self::Pending,
                        fn (Builder $query) => $query->orWhereNull('sent_at'),
                        fn (Builder $query) => $query->orWhereNotNull($this->getFieldName()),
                    )
            );
    }

    protected function getFieldName(): string
    {
        return sprintf('%s_at', $this->value);
    }

    protected function isDefault(): bool
    {
        return match ($this) {
            self::Pending => true,
            default => false,
        };
    }
}
