<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WikiPage;
use Illuminate\Auth\Access\HandlesAuthorization;

class WikiPagePolicy
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

    public function view(User $user, WikiPage $wikiPage): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, WikiPage $wikiPage): bool
    {
        return false;
    }

    public function delete(User $user, WikiPage $wikiPage): bool
    {
        return false;
    }

    public function restore(User $user, WikiPage $wikiPage): bool
    {
        return false;
    }

    public function forceDelete(User $user, WikiPage $wikiPage): bool
    {
        return false;
    }
}
