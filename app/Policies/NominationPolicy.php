<?php

namespace App\Policies;

use App\Models\Elector;
use App\Models\Nomination;
use App\Models\Position;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NominationPolicy
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

    public function view(User $user, Nomination $nomination): bool
    {
        return true;
    }

    public function update(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft;
    }

    public function delete(User $user, Nomination $nomination): bool
    {
        return false;
    }

    public function setTiming(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft &&
            blank(value: $nomination->starts_at) &&
            ($nomination->electors_count ?? $nomination->loadCount(relations: ['electors'])->electors_count) > 0 &&
            ($nomination->positions_count ?? $nomination->loadCount(relations: ['positions'])->positions_count) > 0 &&
            filled(value: $nomination->preference);
    }

    public function updateTiming(User $user, Nomination $nomination): bool
    {
        return ($nomination->is_draft || $nomination->is_published) &&
            filled(value: $nomination->starts_at) &&
            ($nomination->electors_count ?? $nomination->loadCount(relations: ['electors'])->electors_count) > 0 &&
            ($nomination->positions_count ?? $nomination->loadCount(relations: ['positions'])->positions_count) > 0 &&
            filled(value: $nomination->preference);
    }

    public function publish(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft &&
            filled(value: $nomination->starts_at) &&
            filled(value: $nomination->ends_at) &&
            filled(value: $nomination->timezone) &&
            ($nomination->electors_count ?? $nomination->loadCount(relations: ['electors'])->electors_count) > 0 &&
            ($nomination->positions_count ?? $nomination->loadCount(relations: ['positions'])->positions_count) > 0 &&
            filled(value: $nomination->preference);
    }

    public function close(User $user, Nomination $nomination): bool
    {
        return $nomination->is_published;
    }

    public function scrutinise(User $user, Nomination $nomination): bool
    {
        return $nomination->is_closed;
    }

    public function cancel(User $user, Nomination $nomination): bool
    {
        return ! $nomination->is_cancelled &&
            ! $nomination->is_scrutinised;
    }

    public function viewPreference(User $user, Nomination $nomination): bool
    {
        return true;
    }

    public function savePreference(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft;
    }

    public function createElector(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft;
    }

    public function importElector(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft;
    }

    public function viewAnyElector(User $user, Nomination $nomination): bool
    {
        return filled(value: $nomination->preference);
    }

    public function updateAnyElector(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft;
    }

    public function deleteAnyElector(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft;
    }

    public function createPosition(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft;
    }

    public function reorderPosition(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft &&
            ($nomination->positions_count ?? $nomination->loadCount(relations: ['positions'])->positions_count) > 1;
    }

    public function viewAnyPosition(User $user, Nomination $nomination): bool
    {
        return ($nomination->electors_count ?? $nomination->loadCount(relations: ['electors'])->electors_count) > 0;
    }

    public function updateAnyPosition(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft;
    }

    public function deleteAnyPosition(User $user, Nomination $nomination): bool
    {
        return $nomination->is_draft;
    }

    public function approveAnyNominee(User $user, Nomination $nomination): bool
    {
        return $nomination->is_closed;
    }

    public function rejectAnyNominee(User $user, Nomination $nomination): bool
    {
        return $nomination->is_closed;
    }
}
