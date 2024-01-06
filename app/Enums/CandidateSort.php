<?php

namespace App\Enums;

enum CandidateSort: string
{
    case MANUAL = 'manual';
    case RANDOM = 'random';
    case ASCENDING = 'ascending';
    case DESCENDING = 'descending';
}
