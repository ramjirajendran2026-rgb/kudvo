<?php

namespace App\Policies;

use App\Enums\MeetingStatus;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Meeting $meeting): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Meeting $meeting): bool
    {
        return $meeting->status === MeetingStatus::Onboarding &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        return $meeting->status === MeetingStatus::Onboarding &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function downloadDetailedResult(User $user, Meeting $meeting): bool
    {
        return $meeting->status === MeetingStatus::Completed &&
            $this->hasRoleAccess($user, $meeting);
    }

    protected function hasRoleAccess(User $user, Meeting $meeting): bool
    {
        return $meeting->organisation->users()->whereKey($user->getKey())->exists();
    }
}
