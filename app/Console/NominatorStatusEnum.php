<?php

namespace App\Console;

enum NominatorStatusEnum: string
{
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
}
