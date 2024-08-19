<?php

namespace App\Listeners\Election;

use App\Events\Election\ElectorImportCompleted;
use App\Filament\Imports\ElectorImporter;
use App\Models\Election;
use Filament\Actions\Imports\Events\ImportCompleted;

class NotifyElectorImportCompletion
{
    public function __construct() {}

    public function handle(ImportCompleted $event): void
    {
        if (
            $event->getImport()->importer !== ElectorImporter::class ||
            $event->getOptions()['event_type'] !== Election::class ||
            blank($electionId = $event->getOptions()['event_id'])
        ) {
            return;
        }

        broadcast(event: new ElectorImportCompleted(
            electionId: $electionId,
            importId: $event->getImport()->getKey()
        ));
    }
}
