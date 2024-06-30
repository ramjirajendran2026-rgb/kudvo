<?php

namespace App\Filament\Exports;

use App\Models\Elector;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class ElectorBallotExporter extends Exporter
{
    protected static ?string $model = Elector::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make(name: 'membership_number'),

            ExportColumn::make(name: 'full_name'),

            ExportColumn::make(name: 'phone'),

            ExportColumn::make(name: 'email'),

            ExportColumn::make(name: 'ballot.booth.name'),

            ExportColumn::make(name: 'ballot.voted_at')
                ->formatStateUsing(callback: fn (?Carbon $state, array $options) => $state?->timezone(value: $options['timezone'] ?? null))
                ->label(label: 'Voted At'),

            ExportColumn::make(name: 'ballot.ip_address')
                ->label(label: 'IP Address'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(relations: ['ballot', 'ballot.booth']);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your elector export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
