<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WikiTag;
use Illuminate\Auth\Access\HandlesAuthorization;

class WikiTagPolicy
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

    public function view(User $user, WikiTag $wikiTag): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, WikiTag $wikiTag): bool
    {
        return false;
    }

    public function delete(User $user, WikiTag $wikiTag): bool
    {
        return false;
    }

    public function restore(User $user, WikiTag $wikiTag): bool
    {
        return false;
    }

    public function forceDelete(User $user, WikiTag $wikiTag): bool
    {
        return false;
    }
}
