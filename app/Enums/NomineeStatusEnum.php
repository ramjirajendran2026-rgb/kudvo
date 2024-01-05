<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum NomineeStatusEnum: string
    implements HasLabel
{
    case PROPOSED = 'proposed';
    case NOMINATED = 'nominated';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';

    public function getLabel(): ?string
    {
        return Str::title(value: $this->value);
    }
}
