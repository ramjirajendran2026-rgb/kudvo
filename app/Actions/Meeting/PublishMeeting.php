<?php

namespace App\Actions\Meeting;

use App\Models\Meeting;

class PublishMeeting
{
    public function execute(Meeting $meeting): void
    {
        $meeting->published_at = now();
        $meeting->save();
    }
}
