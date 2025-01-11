<?php

namespace App\Notifications\Meeting;

use App\Enums\MailMessagePurpose;
use App\Enums\SmsMessagePurpose;
use App\Models\Meeting;
use App\Models\Participant;
use App\Notifications\Concerns\HasSmsChannel;
use App\Notifications\Contracts\HasMailMessagePurpose;
use App\Notifications\Contracts\HasSmsMessagePurpose;
use App\Settings\SmsTemplates;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class MeetingInvitationNotification extends Notification implements HasMailMessagePurpose, HasSmsMessagePurpose, ShouldQueue
{
    use HasSmsChannel;
    use Queueable;

    public const VAR_MEETING_LINK = '{#MEETING_LINK#}';

    public const VAR_MEETING_LINK_SHORT = '{#MEETING_LINK_SHORT#}';

    public const VAR_MEETING_NAME = '{#MEETING_NAME#}';

    public const VAR_MEETING_NAME_SHORT = '{#MEETING_NAME_SHORT#}';

    public const VAR_PARTICIPANT_NAME = '{#PARTICIPANT_NAME#}';

    public const VAR_PARTICIPANT_NAME_SHORT = '{#PARTICIPANT_NAME_SHORT#}';

    public function __construct(
        protected Participant $participant,
        protected Meeting $meeting,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->prepareSmsChannel(notifiable: $notifiable, via: ['mail', 'sms']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $participant = $this->getParticipant();
        $meeting = $this->getMeeting();

        return (new MailMessage)
            ->subject(subject: "Meeting Link for $meeting->name - $participant->membership_number")
            ->greeting(greeting: "Dear $participant->name,")
            ->line(line: "Use the following link to cast your vote for $meeting->name.")
            ->action(text: 'Vote Now', url: $this->getMeetingLink())
            ->line(line: 'Thank you for using our application!');
    }

    public function toSms(object $notifiable): string
    {
        return $this->formatTemplate(
            template: app(abstract: SmsTemplates::class)->meeting_invitation,
            notifiable: $notifiable
        );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        return ! $this->getParticipant()->is_voted;
    }

    public function getMailMessagePurpose(object $notifiable): MailMessagePurpose
    {
        return MailMessagePurpose::MeetingInvitation;
    }

    public function getSmsMessagePurpose(object $notifiable): SmsMessagePurpose
    {
        return SmsMessagePurpose::MeetingInvitation;
    }

    protected function formatTemplate(string $template, object $notifiable): string
    {
        $variables = [
            static::VAR_MEETING_LINK => $this->getMeetingLink(),
            static::VAR_MEETING_LINK_SHORT => $this->getBallotLinkShort(),
            static::VAR_MEETING_NAME => $this->getMeeting()->name,
            static::VAR_MEETING_NAME_SHORT => Str::maxLimit(value: $this->getMeeting()->name, limit: 30),
            static::VAR_PARTICIPANT_NAME => $this->getParticipant()->name,
            static::VAR_PARTICIPANT_NAME_SHORT => Str::maxLimit(value: $this->getParticipant()->name ?: 'Member', limit: 30),
        ];

        return Str::replace(
            search: array_keys($variables),
            replace: array_values($variables),
            subject: $template
        );
    }

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getMeeting(): Meeting
    {
        return $this->meeting;
    }

    protected function getMeetingLink(): string
    {
        return URL::signedRoute(
            name: 'filament.meeting.eul',
            parameters: [
                'meeting' => $this->getMeeting(),
                'participant' => $this->getParticipant(),
            ]
        );
    }

    protected function getBallotLinkShort(): ?string
    {
        return route(name: 'short_link.go', parameters: ['p' => $this->getParticipant()->short_key]);
    }
}
