<?php

namespace App\Notifications\Election;

use App\Enums\MailMessagePurpose;
use App\Enums\SmsMessagePurpose;
use App\Enums\WhatsAppMessagePurpose;
use App\Models\Ballot;
use App\Models\Election;
use App\Models\Elector;
use App\Notifications\Concerns\HasSmsChannel;
use App\Notifications\Contracts\HasMailMessagePurpose;
use App\Notifications\Contracts\HasSmsMessagePurpose;
use App\Notifications\Contracts\HasWhatsAppMessagePurpose;
use App\Services\WhatsApp\Messages\TemplateComponents\TemplateComponentFactory;
use App\Services\WhatsApp\Messages\WhatsAppMessage;
use App\Services\WhatsApp\Messages\WhatsAppMessageFactory;
use App\Settings\SmsTemplates;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Message;

class VotedConfirmationNotification extends Notification implements HasMailMessagePurpose, HasSmsMessagePurpose, HasWhatsAppMessagePurpose
{
    use HasSmsChannel;

    public const VAR_ELECTION_NAME = '{#ELECTION_NAME#}';

    public const VAR_ELECTION_NAME_SHORT = '{#ELECTION_NAME_SHORT#}';

    public const VAR_ELECTOR_NAME = '{#ELECTOR_NAME#}';

    public const VAR_ELECTOR_NAME_SHORT = '{#ELECTOR_NAME_SHORT#}';

    public const VAR_VOTED_AT = '{#VOTED_AT#}';

    protected Election $election;

    protected Elector $elector;

    public function __construct(
        protected Ballot $ballot,
        protected array $via = [],
    ) {
        $this->elector = $ballot->elector;
        $this->election = $this->elector->event;
    }

    public function via($notifiable): array
    {
        return $this->prepareSmsChannel(notifiable: $notifiable, via: $this->via);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(subject: 'Voted Confirmation for ' . $this->election->getShortName() . ' - ' . $this->elector->membership_number)
            ->greeting(greeting: $this->formatTemplate(template: 'Dear ' . static::VAR_ELECTOR_NAME . ',', notifiable: $notifiable))
            ->line(line: $this->formatTemplate(template: 'You have successfully cast your vote for **' . static::VAR_ELECTION_NAME . '** on **' . static::VAR_VOTED_AT . '**.', notifiable: $notifiable))
            ->withSymfonyMessage(
                callback: fn (Message $message) => $message
                    ->getHeaders()
                    ->addTextHeader('Sensitivity', 'Private')
            );
    }

    public function toSms(object $notifiable): string
    {
        return $this->formatTemplate(
            template: app(abstract: SmsTemplates::class)->elector_voted_confirmation,
            notifiable: $notifiable
        );
    }

    public function toWhatsapp(object $notifiable): WhatsAppMessage
    {
        return WhatsAppMessageFactory::template('ballot_acknowledgement')
            ->addComponent(TemplateComponentFactory::header([
                TemplateComponentFactory::textParameter('Vote Submitted - ' . $this->election->getShortName(), 'header'),
            ]))
            ->addComponent(TemplateComponentFactory::body([
                TemplateComponentFactory::textParameter($this->elector->display_name ?: $this->elector->membership_number, 'member_name'),
                TemplateComponentFactory::textParameter($this->election->name, 'election_name'),
                TemplateComponentFactory::textParameter($this->election->organisation->name, 'organization_name'),
            ]))
            ->addComponent(TemplateComponentFactory::flowButton());
    }

    public function toArray($notifiable): array
    {
        return [];
    }

    protected function formatTemplate(string $template, object $notifiable): string
    {
        $variables = [
            static::VAR_ELECTION_NAME => $this->election->getShortName(),
            static::VAR_ELECTION_NAME_SHORT => Str::maxLimit(value: $this->election->getShortName(), limit: 30),
            static::VAR_ELECTOR_NAME => $this->elector->display_name,
            static::VAR_ELECTOR_NAME_SHORT => Str::maxLimit(value: $this->elector->display_name, limit: 30),
            static::VAR_VOTED_AT => $this->ballot->voted_at->timezone(value: $this->election->timezone)->format(format: 'M j, Y h:i A'),
        ];

        return Str::replace(
            search: array_keys($variables),
            replace: array_values($variables),
            subject: $template
        );
    }

    public function getSmsMessagePurpose(object $notifiable): SmsMessagePurpose
    {
        return SmsMessagePurpose::VotedConfirmation;
    }

    public function getWhatsAppMessagePurpose(object $notifiable): WhatsAppMessagePurpose
    {
        return WhatsAppMessagePurpose::VotedConfirmation;
    }

    public function getMailMessagePurpose(object $notifiable): MailMessagePurpose
    {
        return MailMessagePurpose::VotedConfirmation;
    }
}
