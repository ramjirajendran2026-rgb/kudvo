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
            self::FullAccess => __('app.enums.election_collaborator_permission.full_access.label'),
            self::ReadOnly => __('app.enums.election_collaborator_permission.read_only.label'),
            self::NoAccess => __('app.enums.election_collaborator_permission.no_access.label'),
        };
    }
}
