<?php

namespace App\Actions\Survey;

use App\Models\Survey;

class PublishSurvey
{
    public function execute(Survey $survey): bool
    {
        return $survey->touch('published_at');
    }
}
