<?php

namespace App\Listeners\Meeting;

use App\Events\Meeting\ParticipantImportCompleted;
use App\Filament\Imports\ParticipantImporter;
use Filament\Actions\Imports\Events\ImportCompleted;

class NotifyParticipantImportCompletion
{
    public function __construct() {}

    public function handle(ImportCompleted $event): void
    {
        if (
            $event->getImport()->importer !== ParticipantImporter::class ||
            blank($meetingId = $event->getOptions()['meeting_id'])
        ) {
            return;
        }

        broadcast(event: new ParticipantImportCompleted(
            meetingId: $meetingId,
            importId: $event->getImport()->getKey()
        ));
    }
}
