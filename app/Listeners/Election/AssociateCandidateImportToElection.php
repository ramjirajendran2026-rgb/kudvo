<?php

namespace App\Listeners\Election;

use App\Filament\Imports\CandidateImporter;
use App\Models\Election;
use Filament\Actions\Imports\Events\ImportStarted;

class AssociateCandidateImportToElection
{
    public function __construct() {}

    public function handle(ImportStarted $event): void
    {
        if (
            $event->getImport()->importer !== CandidateImporter::class ||
            blank($electionId = $event->getOptions()['election_id'])
        ) {
            return;
        }

        Election::find(id: $electionId)
            ?->imports()
            ->attach(id: $event->getImport(), attributes: [
                'options' => $event->getOptions(),
                'column_map' => $event->getColumnMap(),
            ]);
    }
}
