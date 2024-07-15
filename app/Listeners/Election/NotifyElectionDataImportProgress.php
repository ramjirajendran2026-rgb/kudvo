<?php

namespace App\Listeners\Election;

use App\Events\Election\ElectionDataImportJobProcessed;
use App\Filament\Imports\CandidateImporter;
use App\Filament\Imports\ElectorImporter;
use App\Models\Election;
use Filament\Actions\Imports\Events\ImportCsvProcessed;
use Illuminate\Database\Eloquent\Builder;

class NotifyElectionDataImportProgress
{
    public function handle(ImportCsvProcessed $event): void
    {
        $import = $event->getImport();

        if (! in_array($import->importer, [ElectorImporter::class, CandidateImporter::class])) {
            return;
        }

        $election = Election::query()
            ->whereHas(relation: 'imports', callback: fn (Builder $query) => $query->whereKey(id: $import->getKey()))
            ->first();

        if (blank($election)) {
            return;
        }

        broadcast(event: new ElectionDataImportJobProcessed(
            electionId: $election->getKey(),
            importId: $event->getImport()->getKey()
        ));
    }
}
