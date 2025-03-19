<?php

namespace App\Actions\Survey;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Support\Str;

class GenerateReferenceNumber
{
    public function execute(SurveyResponse $response, Survey $survey): string
    {
        return $survey->settings?->reference_number_prefix .
            Str::padLeft($response->sort, $survey->settings?->reference_number_pad_length ?? 1, '0');
    }
}
