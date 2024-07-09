<?php

namespace App\Policies;

use App\Models\Election;
use App\Models\Elector;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ElectorPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Elector $elector): bool
    {
        $election = $elector->event;

        if (! $election instanceof Election) {
            return false;
        }

        return $election->is_draft ||
            (
                $election->is_published &&
                $election->preference->elector_update_after_publish &&
                ! $elector->ballot?->isVoted()
            );
    }

    public function sendBallotLink(User $user, Elector $elector): bool
    {
        $election = $elector->event;

        return $election instanceof Election &&
            $election->is_published &&
            $election->preference->isBallotLinkBlastNeeded(elector: $elector) &&
            ! $elector->ballot?->isVoted();
    }
}
