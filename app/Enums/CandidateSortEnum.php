<?php

namespace App\Enums;

enum CandidateSortEnum: string
{
    case MANUAL = 'manual';
    case RANDOM = 'random';
    case ASCENDING = 'ascending';
    case DESCENDING = 'descending';
}
