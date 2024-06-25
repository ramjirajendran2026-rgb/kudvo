<?php

namespace App\Policies;

use App\Models\Election;
use App\Models\Elector;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

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
        return $election->is_draft || $election->is_published;
    }

    public function delete(User $user, Election $election): bool
    {
        return $election->is_draft;
    }

    public function setTiming(User $user, Election $election): bool
    {
        return $election->is_draft &&
            ! $election->isTimingConfigured() &&
            filled($election->preference) &&
            ($election->electors_count ?? $election->loadCount(relations: ['electors'])->electors_count) > 0 &&
            ($election->positions_count ?? $election->loadCount(relations: ['positions'])->positions_count) > 0 &&
            $election->positions()
                ->whereHas(
                    relation: 'candidates',
                    count: DB::raw(value: 'positions.quota')
                )
                ->count() == $election->positions_count;
    }

    public function updateTiming(User $user, Election $election): bool
    {
        return $election->is_published ||
            ($election->is_draft && $election->isTimingConfigured());
    }

    public function publish(User $user, Election $election): bool
    {
        return $election->is_draft &&
            $election->isTimingConfigured() &&
            filled($election->preference) &&
            ($election->electors_count ?? $election->loadCount(relations: ['electors'])->electors_count) > 0 &&
            ($election->positions_count ?? $election->loadCount(relations: ['positions'])->positions_count) > 0 &&
            $election->positions()
                ->whereHas(
                    relation: 'candidates',
                    count: DB::raw(value: 'positions.quota')
                )
                ->count() == $election->positions_count;
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

    public function preview(User $user, Election $election): bool
    {
        return ($election->positions_count ?? $election->loadCount(relations: ['positions'])->positions_count) > 0 &&
            $election->positions()
                ->whereHas(
                    relation: 'candidates',
                    count: DB::raw(value: 'positions.quota')
                )
                ->count() == $election->positions_count;
    }

    public function useAsBoothDevice(User $user, Election $election): bool
    {
        return $election->isBoothVotingEnabled() &&
            ($election->is_draft || $election->is_published);
    }

    public function removeFromBoothDevice(User $user, Election $election): bool
    {
        return $election->is_draft || $election->is_published;
    }

    public function viewPreference(User $user, Election $election): bool
    {
        return true;
    }

    public function savePreference(User $user, Election $election): bool
    {
        return $election->is_draft && ! $election->is_paid;
    }

    public function createElector(User $user, Election $election): bool
    {
        return ($election->is_draft && ! $election->is_paid) ||
            (
                $election->is_published &&
                $election->preference->elector_update_after_publish
            );
    }

    public function importElector(User $user, Election $election): bool
    {
        return $election->is_draft && ! $election->is_paid;
    }

    public function viewAnyElector(User $user, Election $election): bool
    {
        return filled(value: $election->preference);
    }

    public function updateAnyElector(User $user, Election $election): bool
    {
        return ($election->is_draft && ! $election->is_paid) || $election->is_published;
    }

    public function deleteAnyElector(User $user, Election $election): bool
    {
        return $election->is_draft && ! $election->is_paid;
    }

    public function viewBallotSetup(User $user, Election $election): bool
    {
        return ($election->electors_count ?? $election->loadCount(relations: ['electors'])->electors_count) > 0;
    }

    public function createPosition(User $user, Election $election): bool
    {
        if ($user->canAccessPanel(panel: Filament::getPanel(id: 'admin'))) {
            return true;
        }

        return $election->is_draft && ! $election->is_paid;
    }

    public function reorderPosition(User $user, Election $election): bool
    {
        if ($user->canAccessPanel(panel: Filament::getPanel(id: 'admin'))) {
            return true;
        }

        return $election->is_draft && ! $election->is_paid &&
            ($election->positions_count ?? $election->loadCount(relations: ['positions'])->positions_count) > 1;
    }

    public function viewAnyPosition(User $user, Election $election): bool
    {
        return ($election->electors_count ?? $election->loadCount(relations: ['electors'])->electors_count) > 0;
    }

    public function updateAnyPosition(User $user, Election $election): bool
    {
        if ($user->canAccessPanel(panel: Filament::getPanel(id: 'admin'))) {
            return true;
        }

        return $election->is_draft && ! $election->is_paid;
    }

    public function deleteAnyPosition(User $user, Election $election): bool
    {
        if ($user->canAccessPanel(panel: Filament::getPanel(id: 'admin'))) {
            return true;
        }

        return $election->is_draft && ! $election->is_paid;
    }

    public function createCandidate(User $user, Election $election): bool
    {
        if ($user->canAccessPanel(panel: Filament::getPanel(id: 'admin'))) {
            return true;
        }

        return $election->is_draft && ! $election->is_paid;
    }

    public function importCandidate(User $user, Election $election): bool
    {
        return $election->is_draft && ! $election->is_paid;
    }

    public function reorderCandidate(User $user, Election $election): bool
    {
        if ($user->canAccessPanel(panel: Filament::getPanel(id: 'admin'))) {
            return true;
        }

        return $election->is_draft && ! $election->is_paid;
    }

    public function viewAnyCandidate(User $user, Election $election): bool
    {
        return $election->is_draft;
    }

    public function updateAnyCandidate(User $user, Election $election): bool
    {
        if ($user->canAccessPanel(panel: Filament::getPanel(id: 'admin'))) {
            return true;
        }

        return $election->is_draft && ! $election->is_paid;
    }

    public function deleteAnyCandidate(User $user, Election $election): bool
    {
        if ($user->canAccessPanel(panel: Filament::getPanel(id: 'admin'))) {
            return true;
        }

        return $election->is_draft && ! $election->is_paid;
    }

    public function viewAnyMonitorToken(User $user, Election $election): bool
    {
        return ! $election->is_draft;
    }

    public function viewAnyBoothToken(User $user, Election $election): bool
    {
        return ! $election->is_draft && $election->isBoothVotingEnabled();
    }

    public function generateResult(User $user, Election $election): bool
    {
        return $election->is_closed && blank($election->result);
    }

    public function viewResult(User $user, Election $election): bool
    {
        return $election->is_completed;
    }

    public function sendBallotLinkBulk(User $user, Election $election): bool
    {
        return $election->is_published;
    }

    public function vote(Elector $elector, Election $election): bool
    {
        return $election->is_open &&
            (
                ! $elector->ballot?->isVoted() ||
                $election->preference->voted_ballot_update
            );
    }

    public function boothVote(Elector $elector, Election $election): bool
    {
        return $election->is_booth_open &&
            (
                ! $elector->ballot?->isVoted() ||
                $election->preference->voted_ballot_update
            );
    }
}
