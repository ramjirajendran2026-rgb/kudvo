<?php

namespace App\Enums;

enum MeetingVotingStatus: string
{
    case Pending = 'pending';
    case Scheduled = 'scheduled';
    case Open = 'open';
    case Ended = 'ended';
    case Closed = 'closed';
}
