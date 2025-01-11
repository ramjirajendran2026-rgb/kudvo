<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ResolutionChoice: string implements HasColor, HasIcon, HasLabel
{
    case For = 'for';
    case Against = 'against';
    case Abstain = 'abstain';

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::For => 'success',
            self::Against => 'danger',
            self::Abstain => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::For => 'heroicon-m-hand-thumb-up',
            self::Against => 'heroicon-m-hand-thumb-down',
            self::Abstain => 'heroicon-m-minus',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::For => 'For',
            self::Against => 'Against',
            self::Abstain => 'Abstain',
        };
    }

    public function getLabelSuggestions(): array
    {
        return match ($this) {
            self::For => ['For', 'Approve', 'Yes', 'Agreed', 'In favor', 'Accept', 'Agree'],
            self::Against => ['Against', 'Disapprove', 'No', 'Disagreed', 'Reject', 'Disagree'],
            self::Abstain => ['Abstain', 'Abstain from voting'],
        };
    }
}
