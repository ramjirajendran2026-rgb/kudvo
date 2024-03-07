<?php

namespace App\Notifications\Election;

use App\Models\Election;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VotingInstructionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Election $election
    )
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        config(['app.name' => 'SecuredVoting']);

        return (new MailMessage)
            ->from(address: config('mail.from.address'), name: config('app.name'))
            ->subject(subject: 'Voting Instructions for '.$this->election->name)
            ->greeting(greeting: 'Hello!')
            ->line(line: 'Watch the video below to learn how to vote in the '.$this->election->name.'.')
            ->action(text: 'Watch Video', url: 'https://www.canva.com/design/DAF9T69NiOQ/BP4ICg78WjUVnj9gGfCkdg/watch')
            ->line(line: 'If you have any questions, please call us at +91 9360364115 / +91 9715272625 / +91 9043041403.');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
