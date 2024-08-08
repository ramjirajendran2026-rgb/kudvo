<?php

namespace App\Listeners\Auth;

use App\Enums\RolesEnum;
use App\Models\User;
use Filament\Events\Auth\Registered;

class AssignRoleToRegisteredUser
{
    public function handle(Registered $event): void
    {
        $user = $event->getUser();

        if (! ($user instanceof User)) {
            return;
        }

        $user->assignRole(RolesEnum::User);
    }
}
