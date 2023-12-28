<?php

namespace App\Enums;

enum NomineeStatusEnum: string
{
    case PROPOSED = 'proposed';
    case NOMINATED = 'nominated';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';
}
