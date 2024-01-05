<?php

namespace App\Notifications\Nomination;

use App\Models\Elector;
use App\Models\Nomination;
use App\Models\Nominee;
use App\Models\Position;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NomineeRequestNotification extends Notification
{
    protected Position $position;
    protected Nomination $nomination;

    public function __construct(
        protected Nominee $nominee,
    )
    {
        $this->position = $this->nominee->position;
        $this->nomination = $this->position->event;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line(line: $this->getLine1())
            ->line(line: 'Use the below button to accept / decline.')
            ->action('Accept / Decline', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }

    protected function getLine1(): string
    {
        return 'Your co member '.
            $this->nominee->proposer?->display_name.
            ' nominated you for the position of '.
            $this->position->name.
            ' to the upcoming '.
            $this->nomination->name.
            '.';
    }
}
