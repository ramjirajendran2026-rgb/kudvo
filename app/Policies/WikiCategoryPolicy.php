<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WikiCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class WikiCategoryPolicy
{
    use HandlesAuthorization;

    public static function before(?User $user, string $ability): ?bool
    {
        if ($user?->hasStaffRole()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, WikiCategory $wikiCategory): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, WikiCategory $wikiCategory): bool
    {
        return false;
    }

    public function delete(User $user, WikiCategory $wikiCategory): bool
    {
        return false;
    }

    public function restore(User $user, WikiCategory $wikiCategory): bool
    {
        return false;
    }

    public function forceDelete(User $user, WikiCategory $wikiCategory): bool
    {
        return false;
    }
}
