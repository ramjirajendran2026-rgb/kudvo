<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SurveyResponsesPageTabs: string implements HasLabel
{
    case Summary = 'summary';
    case Individual = 'individual';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Summary => 'Summary',
            self::Individual => 'Individual',
        };
    }
}
