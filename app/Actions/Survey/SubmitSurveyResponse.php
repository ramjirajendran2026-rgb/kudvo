<?php

namespace App\Actions\Survey;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Support\Str;

class SubmitSurveyResponse
{
    public function execute(Survey $survey, array $data): SurveyResponse
    {
        /** @var SurveyResponse $response */
        $response = $survey->responses()->create();

        $data = collect($data)->filter()->filter(fn ($value, $key) => ! Str::endsWith($key, '_otp'))->map(fn ($value, $key) => [
            'question_id' => Str::replaceStart(SurveyQuestion::KEY_PREFIX, '', $key),
            'content' => $value,
        ])->values()->all();

        $response->answers()->createMany($data);

        return $response;
    }
}
