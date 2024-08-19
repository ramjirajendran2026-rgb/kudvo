<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RolesEnum: string implements HasLabel
{
    case Admin = 'admin';
    case Staff = 'staff';
    case User = 'user';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Staff => 'Staff',
            self::User => 'User',
        };
    }
}
