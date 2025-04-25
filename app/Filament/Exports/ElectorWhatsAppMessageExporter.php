<?php

namespace App\Filament\Exports;

use App\Enums\WhatsAppMessageStatus;
use App\Models\WhatsAppMessage;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class ElectorWhatsAppMessageExporter extends Exporter
{
    protected static ?string $model = WhatsAppMessage::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make(name: 'whatsappable.membership_number')
                ->label(label: 'Membership Number'),

            ExportColumn::make(name: 'whatsappable.full_name')
                ->label(label: 'Elector Name'),

            ExportColumn::make(name: 'phone')
                ->label(label: 'Phone'),

            ExportColumn::make(name: 'status')
                ->formatStateUsing(callback: fn (?WhatsAppMessageStatus $state) => $state?->value),

            ExportColumn::make(name: 'created_at')
                ->formatStateUsing(callback: fn (?Carbon $state, array $options) => $state?->timezone(value: $options['timezone'] ?? null))
                ->label(label: 'Sent at'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(relations: ['whatsappable']);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your WhatsApp message export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
