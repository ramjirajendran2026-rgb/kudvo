<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ElectionCollaboratorPermission: string implements HasLabel
{
    case FullAccess = 'full_access';
    case ReadOnly = 'read_only';
    case NoAccess = 'no_access';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FullAccess => 'Full Access',
            self::ReadOnly => 'Read Only',
            self::NoAccess => 'No Access',
        };
    }
}
