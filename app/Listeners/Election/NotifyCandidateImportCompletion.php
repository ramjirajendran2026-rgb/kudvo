<?php

namespace App\Listeners\Election;

use App\Events\Election\CandidateImportCompleted;
use App\Filament\Imports\CandidateImporter;
use Filament\Actions\Imports\Events\ImportCompleted;

class NotifyCandidateImportCompletion
{
    public function __construct() {}

    public function handle(ImportCompleted $event): void
    {
        if (
            $event->getImport()->importer !== CandidateImporter::class ||
            blank($electionId = $event->getOptions()['election_id'])
        ) {
            return;
        }

        broadcast(event: new CandidateImportCompleted(
            electionId: $electionId,
            importId: $event->getImport()->getKey()
        ));
    }
}
