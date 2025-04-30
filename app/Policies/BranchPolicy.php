<?php

namespace App\Policies;

use App\Models\Organisation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;

class BranchPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        $organisation = Filament::getTenant();

        if ($organisation instanceof Organisation) {
            return $organisation->settings?->allow_branches ?? false;
        }

        return false;
    }
}
