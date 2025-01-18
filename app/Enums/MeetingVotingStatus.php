<?php

namespace App\Enums;

enum MeetingVotingStatus: string
{
    case NotApplicable = 'not_applicable';
    case Scheduled = 'scheduled';
    case Open = 'open';
    case Ended = 'ended';
    case Closed = 'closed';
}
