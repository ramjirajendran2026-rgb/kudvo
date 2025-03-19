<?php

namespace App\Notifications\Survey;

use App\Actions\Survey\GenerateReferenceNumber;
use App\Enums\MailMessagePurpose;
use App\Enums\SmsMessagePurpose;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Notifications\Concerns\HasSmsChannel;
use App\Notifications\Contracts\HasMailMessagePurpose;
use App\Notifications\Contracts\HasSmsMessagePurpose;
use App\Settings\SmsTemplates;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Message;

class AcknowledgementNotification extends Notification implements HasMailMessagePurpose, HasSmsMessagePurpose
{
    use HasSmsChannel;

    public const VAR_SURVEY_NAME = '{#SURVEY_NAME#}';

    public const VAR_SURVEY_NAME_SHORT = '{#SURVEY_NAME_SHORT#}';

    public const VAR_REFERENCE_NUMBER = '{#REFERENCE_NUMBER#}';

    public function __construct(
        protected Survey $survey,
        protected SurveyResponse $response,
        protected array $via = [],
    ) {}

    public function via($notifiable): array
    {
        return $this->prepareSmsChannel(notifiable: $notifiable, via: $this->via);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line(line: $this->formatTemplate(template: 'Thank you for registering. Your reference number is  ' . static::VAR_REFERENCE_NUMBER . '.', notifiable: $notifiable))
            ->withSymfonyMessage(
                callback: fn (Message $message) => $message
                    ->getHeaders()
                    ->addTextHeader('Sensitivity', 'Private')
            );
    }

    protected function formatTemplate(string $template, object $notifiable): string
    {
        $variables = [
            static::VAR_SURVEY_NAME => $this->survey->title,
            static::VAR_SURVEY_NAME_SHORT => Str::maxLimit(value: $this->survey->title, limit: 30),
            static::VAR_REFERENCE_NUMBER => app(GenerateReferenceNumber::class)->execute($this->response, $this->survey),
        ];

        return Str::replace(
            search: array_keys($variables),
            replace: array_values($variables),
            subject: $template
        );
    }

    public function toSms(object $notifiable): string
    {
        return $this->formatTemplate(
            template: app(abstract: SmsTemplates::class)->survey_acknowledgement,
            notifiable: $notifiable
        );
    }

    public function toArray($notifiable): array
    {
        return [];
    }

    public function getSmsMessagePurpose(object $notifiable): SmsMessagePurpose
    {
        return SmsMessagePurpose::SurveyAcknowledgement;
    }

    public function getMailMessagePurpose(object $notifiable): MailMessagePurpose
    {
        return MailMessagePurpose::SurveyAcknowledgement;
    }
}
