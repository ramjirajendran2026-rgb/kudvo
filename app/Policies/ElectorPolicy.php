<?php

namespace App\Policies;

use App\Models\Election;
use App\Models\Elector;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ElectorPolicy
{
    use HandlesAuthorization;

    public function sendBallotLink(User $user, Elector $elector): bool
    {
        $election = $elector->event;

        return $election instanceof Election &&
            $election->is_published &&
            ! $elector->ballot?->isVoted();
    }
}
