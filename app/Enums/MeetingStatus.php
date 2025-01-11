<?php

namespace App\Enums;

enum MeetingStatus: string
{
    case Onboarding = 'onboarding';
    case Published = 'published';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
}
