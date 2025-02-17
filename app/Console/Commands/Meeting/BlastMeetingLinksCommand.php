<?php

namespace App\Console\Commands\Meeting;

use App\Events\MeetingLinkBlastCompleted;
use App\Events\MeetingLinkBlastInitiated;
use App\Models\Meeting;
use App\Models\MeetingLinkBlast;
use App\Models\Participant;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class BlastMeetingLinksCommand extends Command
{
    protected $signature = 'meeting:blast-participation-links';

    protected $description = 'Blast meeting links';

    public function handle(): void
    {
        MeetingLinkBlast::query()->pending()
            ->activeMeeting()
            ->chunkById(count: 50, callback: function (Collection $blasts) {
                foreach ($blasts as $blast) {

                    /** @var MeetingLinkBlast $blast */
                    $blast->touch(attribute: 'initiated_at');

                    MeetingLinkBlastInitiated::dispatch($blast);

                    /** @var Meeting $meeting */
                    $meeting = $blast->meeting;
                    $meeting->participants()
                        ->chunkById(
                            count: 50,
                            callback: fn (Collection $collection) => $collection
                                ->each(
                                    callback: fn (Participant $participant) => $participant
                                        ->sendParticipationLink(meeting: $meeting)
                                )
                        );

                    $blast->touch(attribute: 'completed_at');

                    MeetingLinkBlastCompleted::dispatch($blast);
                }
            });
    }
}
