<?php

namespace App\Listeners\Meeting;

use App\Events\Meeting\MeetingDataImportChunkProcessed;
use App\Filament\Imports\ParticipantImporter;
use App\Models\Meeting;
use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Illuminate\Database\Eloquent\Builder;

class NotifyMeetingDataImportProgress
{
    public function handle(ImportChunkProcessed $event): void
    {
        if ($event->getImport()->importer !== ParticipantImporter::class) {
            return;
        }

        $meeting = Meeting::query()
            ->whereHas(relation: 'imports', callback: fn (Builder $query) => $query->whereKey(id: $event->getImport()->getKey()))
            ->first();

        if (blank($meeting)) {
            return;
        }

        broadcast(event: new MeetingDataImportChunkProcessed(
            meetingId: $meeting->getKey(),
            importId: $event->getImport()->getKey()
        ));
    }
}
