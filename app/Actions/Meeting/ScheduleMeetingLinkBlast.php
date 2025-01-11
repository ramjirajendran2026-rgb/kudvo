<?php

namespace App\Actions\Meeting;

use App\Models\Meeting;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class ScheduleMeetingLinkBlast
{
    public function execute(Meeting $meeting, string | CarbonInterface | null $scheduledAt = null): void
    {
        if (is_string($scheduledAt)) {
            $scheduledAt = Carbon::parse($scheduledAt);
        }

        $meeting->meetingLinkBlasts()
            ->create([
                'scheduled_at' => $scheduledAt ?? now(),
            ]);
    }
}
