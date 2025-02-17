<?php

namespace App\Listeners\Meeting;

use App\Filament\Imports\ParticipantImporter;
use App\Models\Meeting;
use Filament\Actions\Imports\Events\ImportStarted;

class AssociateParticipantImportToMeeting
{
    public function __construct() {}

    public function handle(ImportStarted $event): void
    {
        if (
            $event->getImport()->importer !== ParticipantImporter::class ||
            blank($meetingId = $event->getOptions()['meeting_id'])
        ) {
            return;
        }

        Meeting::find(id: $meetingId)
            ?->imports()
            ->attach(id: $event->getImport(), attributes: [
                'options' => $event->getOptions(),
                'column_map' => $event->getColumnMap(),
            ]);
    }
}
