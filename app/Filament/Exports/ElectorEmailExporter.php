<?php

namespace App\Filament\Exports;

use App\Enums\EmailStatus;
use App\Models\Email;
use Carbon\CarbonImmutable;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class ElectorEmailExporter extends Exporter
{
    protected static ?string $model = Email::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make(name: 'notifiable.membership_number')
                ->label(label: 'Membership Number'),

            ExportColumn::make(name: 'notifiable.full_name')
                ->label(label: 'Elector Name'),

            ExportColumn::make(name: 'to_address')
                ->label(label: 'Email Address'),

            ExportColumn::make(name: 'subject')
                ->label(label: 'Subject'),

            ExportColumn::make(name: 'status')
                ->formatStateUsing(callback: fn (?EmailStatus $state) => $state?->getLabel()),

            ExportColumn::make(name: 'sent_at')
                ->formatStateUsing(callback: fn (?CarbonImmutable $state, array $options) => $state?->timezone(value: $options['timezone'] ?? null)),

            ExportColumn::make(name: 'message_id'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(relations: ['notifiable']);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your email export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
