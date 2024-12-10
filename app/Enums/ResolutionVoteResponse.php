<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ResolutionVoteResponse: string implements HasDescription, HasIcon, HasLabel
{
    case For = 'for';
    case Against = 'against';
    case Abstain = 'abstain';

    public function getDescription(): ?string
    {
        return match ($this) {
            self::For => 'I approve this',
            self::Against => 'I do not approve this',
            self::Abstain => 'I abstain from voting on this'
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::For => 'heroicon-o-check',
            self::Against => 'heroicon-o-x-mark',
            self::Abstain => 'heroicon-o-minus'
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::For => 'For',
            self::Against => 'Against',
            self::Abstain => 'Abstain'
        };
    }
}
