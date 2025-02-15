<?php

namespace App\Policies;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SurveyPolicy
{
    use HandlesAuthorization;

    public function publish(User $user, Survey $survey): bool
    {
        return ! $survey->is_published;
    }

    public function preview(?User $user, Survey $survey): bool
    {
        if (! $survey->isPreviewable() || blank($user)) {
            return false;
        }

        return true;
    }

    public function createResponse(?User $user, Survey $survey): bool
    {
        return $survey->is_published && $survey->is_active;
    }

    public function viewResponses(User $user, Survey $survey): bool
    {
        return $survey->is_published;
    }
}
