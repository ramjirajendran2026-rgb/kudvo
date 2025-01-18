<?php

namespace App\Policies;

use App\Enums\MeetingStatus;
use App\Enums\MeetingVotingStatus;
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
        return $meeting->isStatus([MeetingStatus::Onboarding, MeetingStatus::Published]) &&
            ! $meeting->isVotingStatus(MeetingVotingStatus::Closed) &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Onboarding) &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function closeVoting(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Published) &&
            $meeting->isVotingStatus([MeetingVotingStatus::Open, MeetingVotingStatus::Ended]) &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function downloadResult(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus([MeetingStatus::Published, MeetingStatus::Completed]) &&
            $meeting->isVotingStatus(MeetingVotingStatus::Closed) &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function downloadDetailedResult(User $user, Meeting $meeting): bool
    {
        return $this->downloadResult($user, $meeting);
    }

    protected function hasRoleAccess(User $user, Meeting $meeting): bool
    {
        return User::whereKey($user->getKey())
            ->whereRelation('organisations', 'id', $meeting->organisation_id)
            ->exists();
    }
}
