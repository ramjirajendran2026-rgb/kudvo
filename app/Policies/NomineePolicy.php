<?php

namespace App\Policies;

use App\Enums\NominatorStatus;
use App\Models\Elector;
use App\Models\Nomination;
use App\Models\Nominee;
use Illuminate\Auth\Access\HandlesAuthorization;

class NomineePolicy
{
    use HandlesAuthorization;

    protected function getNomination(Elector $elector): Nomination
    {
        return $elector->event;
    }

    public function create(Elector $elector): bool
    {
        return $this->getNomination($elector)->is_published;
    }

    public function accept(Elector $elector, Nominee $nominee): bool
    {
        if (! $this->getNomination($elector)->is_published) {
            return false;
        }

        if ($elector->is($nominee->elector)) {
            return $nominee->isPending();
        }

        return $nominee->nominators()
            ->where('status', NominatorStatus::PENDING)
            ->whereBelongsTo($elector)
            ->exists();
    }
}
