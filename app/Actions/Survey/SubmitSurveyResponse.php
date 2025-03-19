<?php

namespace App\Actions\Survey;

use App\Enums\SurveyQuestionType;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Notifications\Survey\AcknowledgementNotification;
use Illuminate\Support\Facades\Notification;
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

        $verifiedPhoneQuestionIds = $survey->questions()
            ->where('type', SurveyQuestionType::VerifiedPhone)
            ->get()
            ->filter(fn (SurveyQuestion $surveyQuestion) => $surveyQuestion->settings['verified_phone']['send_acknowledgement'] ?? false)
            ->pluck('id');

        $verifiedPhoneAnswers = collect($data)
            ->filter(fn ($value, $key) => $verifiedPhoneQuestionIds->contains($value['question_id']))
            ->all();

        foreach ($verifiedPhoneAnswers as $verifiedPhoneAnswer) {
            Notification::route('sms', $verifiedPhoneAnswer['content'])
                ->notifyNow(new AcknowledgementNotification($survey, $response, ['sms']));
        }

        return $response;
    }
}
