<?php

namespace App\Listeners\Election;

use App\Filament\Imports\ElectorImporter;
use App\Models\Election;
use Filament\Actions\Imports\Events\ImportInitiated;

class AssociateElectorImportToElection
{
    public function __construct() {}

    public function handle(ImportInitiated $event): void
    {
        if (
            $event->getImport()->importer !== ElectorImporter::class ||
            $event->getOptions()['event_type'] !== Election::class ||
            blank($electionId = $event->getOptions()['event_id'])
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
