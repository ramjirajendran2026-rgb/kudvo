<?php

namespace App\Filament\User\Resources\MeetingResource\Widgets;

use App\Events\Meeting\MeetingDataImportChunkProcessed;
use App\Models\Meeting;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Illuminate\Support\Number;

class ParticipantDataImportProgress extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.user.resources.meeting-resource.widgets.participant-data-import-progress';

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public Meeting $meeting;

    public Import $import;

    public function mount(Import $import, Meeting $meeting): void
    {
        $this->sync();
    }

    protected function getListeners(): array
    {
        return [
            'echo-private:meetings.' . $this->meeting->id . ',.' . MeetingDataImportChunkProcessed::getBroadcastName() => 'sync',
        ];
    }

    public function sync(): void
    {
        $this->import = $this->meeting->imports()->whereKey(id: $this->import->getKey())->first();
    }

    public function getImport(): Import
    {
        return $this->import;
    }

    public function getProgress(): string
    {
        return $this->getImport()->processed_rows . '/' . $this->getImport()->total_rows . ' (' . $this->getPercentage() . ')';
    }

    public function getPercentage(): string
    {
        return Number::percentage(number: ($this->getImport()->processed_rows / $this->getImport()->total_rows) * 100);
    }

    public function getHeading(): string
    {
        if (blank($this->import->completed_at)) {
            return 'Importing...';
        }

        return $this->getImporter()::getCompletedNotificationTitle(import: $this->import);
    }

    public function getDescription(): ?string
    {
        if (blank($this->import->completed_at)) {
            return null;
        }

        return $this->getImporter()::getCompletedNotificationBody(import: $this->import);
    }

    public function getImporter(): Importer
    {
        return $this->getImport()->getImporter(
            columnMap: $this->import->pivot->column_map,
            options: $this->import->pivot->options,
        );
    }

    public function downloadFailedRowsAction()
    {
        $failedRowsCount = $this->getImport()->getFailedRowsCount();

        return Action::make(name: 'downloadFailedRows')
            ->label(trans_choice('filament-actions::import.notifications.completed.actions.download_failed_rows_csv.label', $failedRowsCount, [
                'count' => Number::format($failedRowsCount),
            ]))
            ->color('danger')
            ->link()
            ->visible(condition: fn () => filled($this->import->completed_at) && $failedRowsCount > 0)
            ->url(route('filament.imports.failed-rows.download', ['import' => $this->import], absolute: false), shouldOpenInNewTab: true);
    }
}
