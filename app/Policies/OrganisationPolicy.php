<?php

namespace App\Policies;

use App\Models\Organisation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganisationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Organisation $organisation): bool
    {
        return $organisation->users()->whereKey($user)->count() > 0;
    }

    public function create(User $user): bool
    {
        return $user->canAccessPanel(panel: Filament::getPanel(id: 'admin')) ||
            $user->organisations()->count() < 1;
    }

    public function update(User $user, Organisation $organisation): bool
    {
        return $organisation->users()->whereKey($user)->exists();
    }
}
