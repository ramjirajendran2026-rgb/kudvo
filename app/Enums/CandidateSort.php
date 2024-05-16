<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum CandidateSort: string implements HasLabel
{
    case MANUAL = 'manual';
    case RANDOM = 'random';
    case ASCENDING = 'ascending';
    case DESCENDING = 'descending';

    public function getLabel(): ?string
    {
        return __('app.enums.candidate_sort.' . $this->value . '.label');
    }
}
