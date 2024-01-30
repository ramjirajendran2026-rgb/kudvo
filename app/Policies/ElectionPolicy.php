<?php

namespace App\Policies;

use App\Models\Election;
use App\Models\Elector;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ElectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, Election $election): bool
    {
        return true;
    }

    public function update(User $user, Election $election): bool
    {
        return $election->is_draft;
    }

    public function delete(User $user, Election $election): bool
    {
        return false;
    }

    public function setTiming(User $user, Election $election): bool
    {
        return $election->is_draft &&
            blank(value: $election->starts_at) &&
            ($election->electors_count ?? $election->loadCount(relations: ['electors'])->electors_count) > 0 &&
            ($election->positions_count ?? $election->loadCount(relations: ['positions'])->positions_count) > 0 &&
            filled(value: $election->preference);
    }

    public function updateTiming(User $user, Election $election): bool
    {
        return ($election->is_draft || $election->is_published) &&
            filled(value: $election->starts_at) &&
            ($election->electors_count ?? $election->loadCount(relations: ['electors'])->electors_count) > 0 &&
            ($election->positions_count ?? $election->loadCount(relations: ['positions'])->positions_count) > 0 &&
            filled(value: $election->preference);
    }

    public function publish(User $user, Election $election): bool
    {
        return $election->is_draft &&
            filled(value: $election->starts_at) &&
            filled(value: $election->ends_at) &&
            filled(value: $election->timezone) &&
            ($election->electors_count ?? $election->loadCount(relations: ['electors'])->electors_count) > 0 &&
            ($election->positions_count ?? $election->loadCount(relations: ['positions'])->positions_count) > 0 &&
            filled(value: $election->preference);
    }

    public function close(User $user, Election $election): bool
    {
        return $election->is_published;
    }

    public function scrutinise(User $user, Election $election): bool
    {
        return $election->is_closed;
    }

    public function cancel(User $user, Election $election): bool
    {
        return ! $election->is_cancelled &&
            ! $election->is_completed;
    }

    public function viewPreference(User $user, Election $election): bool
    {
        return true;
    }

    public function savePreference(User $user, Election $election): bool
    {
        return $election->is_draft;
    }

    public function createElector(User $user, Election $election): bool
    {
        return $election->is_draft;
    }

    public function importElector(User $user, Election $election): bool
    {
        return $election->is_draft;
    }

    public function viewAnyElector(User $user, Election $election): bool
    {
        return filled(value: $election->preference);
    }

    public function updateAnyElector(User $user, Election $election): bool
    {
        return $election->is_draft;
    }

    public function deleteAnyElector(User $user, Election $election): bool
    {
        return $election->is_draft;
    }

    public function viewBallotSetup(User $user, Election $election): bool
    {
        return ($election->electors_count ?? $election->loadCount(relations: ['electors'])->electors_count) > 0;
    }

    public function createPosition(User $user, Election $election): bool
    {
        return $election->is_draft;
    }

    public function reorderPosition(User $user, Election $election): bool
    {
        return $election->is_draft &&
            ($election->positions_count ?? $election->loadCount(relations: ['positions'])->positions_count) > 1;
    }

    public function viewAnyPosition(User $user, Election $election): bool
    {
        return ($election->electors_count ?? $election->loadCount(relations: ['electors'])->electors_count) > 0;
    }

    public function updateAnyPosition(User $user, Election $election): bool
    {
        return $election->is_draft;
    }

    public function deleteAnyPosition(User $user, Election $election): bool
    {
        return $election->is_draft;
    }

    public function vote(Elector $elector, Election $election): bool
    {
        if (! $election->is_open || $elector->ballot?->isVoted()) {
            return false;
        }

        return true;
    }
}
