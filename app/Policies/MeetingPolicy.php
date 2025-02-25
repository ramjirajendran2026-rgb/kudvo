<?php

namespace App\Policies;

use App\Enums\MeetingLinkBlastStatus;
use App\Enums\MeetingStatus;
use App\Enums\MeetingVotingStatus;
use App\Models\Meeting;
use App\Models\MeetingLinkBlast;
use App\Models\Participant;
use App\Models\Resolution;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingPolicy
{
    use HandlesAuthorization;

    public function before(User | Participant | null $user, string $ability): ?bool
    {
        if ($user instanceof User && $user->hasAdminRole()) {
            return match ($ability) {
                'viewAny',
                'view',
                'create',
                'update',
                'delete',
                'cancel',
                'extendVotingTime',
                'closeVoting',
                'downloadResult',
                'downloadDetailedResult',
                'viewAnyParticipant',
                'createParticipant',
                'importParticipant',
                'generateDummyParticipants',
                'updateParticipant',
                'deleteParticipant',
                'deleteAnyParticipant',
                'createResolution',
                'previewResolution',
                'reorderResolution',
                'updateResolution',
                'deleteResolution',
                'createLinkBlast',
                'updateLinkBlast',
                'deleteLinkBlast' => true,
                default => null,
            };
        }

        return null;
    }

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

    public function cancel(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Published) &&
            $meeting->isVotingStatus([MeetingVotingStatus::Scheduled, MeetingVotingStatus::Open, MeetingVotingStatus::Ended]) &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function extendVotingTime(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Published) &&
            $meeting->isVotingStatus([MeetingVotingStatus::Open, MeetingVotingStatus::Ended]) &&
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

    public function viewAnyParticipant(User $user, Meeting $meeting): bool
    {
        return $this->hasRoleAccess($user, $meeting);
    }

    public function createParticipant(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Onboarding) && $this->hasRoleAccess($user, $meeting);
    }

    public function importParticipant(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Onboarding) && $this->hasRoleAccess($user, $meeting);
    }

    public function generateDummyParticipants(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Onboarding) && $user->hasAdminRole();
    }

    public function updateParticipant(User $user, Meeting $meeting, Participant $participant): bool
    {
        return (
            $meeting->isStatus(MeetingStatus::Onboarding) ||
            (
                $meeting->isStatus(MeetingStatus::Published) &&
                $meeting->isVotingStatus([MeetingVotingStatus::Scheduled, MeetingVotingStatus::Open]) &&
                ! $participant->is_voted
            )
        ) && $this->hasRoleAccess($user, $meeting);
    }

    public function deleteParticipant(User $user, Meeting $meeting, Participant $participant): bool
    {
        return $meeting->isStatus(MeetingStatus::Onboarding) &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function deleteAnyParticipant(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Onboarding) &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function notifyParticipantMeetingLinkAny(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Published) &&
            $meeting->isVotingStatus([MeetingVotingStatus::Scheduled, MeetingVotingStatus::Open]) &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function notifyParticipantMeetingLink(User $user, Meeting $meeting, Participant $participant): bool
    {
        return $meeting->isStatus(MeetingStatus::Published) &&
            $meeting->isVotingStatus([MeetingVotingStatus::Scheduled, MeetingVotingStatus::Open]) &&
            ! $participant->is_voted &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function createResolution(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Onboarding) && $this->hasRoleAccess($user, $meeting);
    }

    public function previewResolution(User $user, Meeting $meeting): bool
    {
        return $this->hasRoleAccess($user, $meeting) &&
            $meeting->resolutions()->exists();
    }

    public function reorderResolution(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Onboarding) &&
            $this->hasRoleAccess($user, $meeting) &&
            $meeting->resolutions()->count() > 1;
    }

    public function updateResolution(User $user, Meeting $meeting, Resolution $resolution): bool
    {
        return $meeting->isStatus(MeetingStatus::Onboarding) &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function deleteResolution(User $user, Meeting $meeting, Resolution $resolution): bool
    {
        return $meeting->isStatus(MeetingStatus::Onboarding) &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function createLinkBlast(User $user, Meeting $meeting): bool
    {
        return $meeting->isStatus(MeetingStatus::Published) &&
            $meeting->isVotingStatus([MeetingVotingStatus::Scheduled, MeetingVotingStatus::Open]) &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function updateLinkBlast(User $user, Meeting $meeting, MeetingLinkBlast $blast): bool
    {
        return $meeting->isStatus(MeetingStatus::Published) &&
            $meeting->isVotingStatus([MeetingVotingStatus::Scheduled, MeetingVotingStatus::Open]) &&
            $blast->status === MeetingLinkBlastStatus::Scheduled &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function deleteLinkBlast(User $user, Meeting $meeting, MeetingLinkBlast $blast): bool
    {
        return $meeting->isStatus(MeetingStatus::Published) &&
            $meeting->isVotingStatus([MeetingVotingStatus::Scheduled, MeetingVotingStatus::Open]) &&
            $blast->status === MeetingLinkBlastStatus::Scheduled &&
            $this->hasRoleAccess($user, $meeting);
    }

    public function submitResolutionResponse(Participant $participant, Meeting $meeting): bool
    {
        return ! $participant->is_voted &&
            $meeting->isStatus(MeetingStatus::Published) &&
            $meeting->isVotingStatus(MeetingVotingStatus::Open);
    }

    protected function hasRoleAccess(User $user, Meeting $meeting): bool
    {
        if ($user->hasAdminRole()) {
            return true;
        }

        return User::whereKey($user->getKey())
            ->whereRelation('organisations', 'id', $meeting->organisation_id)
            ->exists();
    }
}
