<?php

namespace App\Filament\User\Resources\ElectionResource\Widgets;

use App\Events\Election\ElectionDataImportJobProcessed;
use App\Models\Election;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Illuminate\Support\Number;

class ElectorDataImportProgress extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.user.resources.election-resource.widgets.import-progress';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public Election $election;

    public Import $import;

    public function mount(Import $import, Election $election): void
    {
        $this->sync();
    }

    protected function getListeners(): array
    {
        return [
            'echo-private:elections.'.$this->election->id.',.'.ElectionDataImportJobProcessed::getBroadcastName() => 'sync',
        ];
    }

    public function sync(): void
    {
        $this->import = $this->election->imports()->whereKey(id: $this->import->getKey())->first();
    }

    public function getImport(): Import
    {
        return $this->import;
    }

    public function getProgress(): string
    {
        return $this->getImport()->processed_rows.'/'.$this->getImport()->total_rows.' ('.$this->getPercentage().')';
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
