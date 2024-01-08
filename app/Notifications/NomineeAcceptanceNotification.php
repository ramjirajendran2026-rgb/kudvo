<?php

namespace App\Notifications;

use App\Models\Nomination;
use App\Models\Nominee;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NomineeAcceptanceNotification extends Notification
{
    public function __construct(
        public Nominee $nominee
    )
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $nominee = $this->nominee;
        $position = $nominee->position;
        $proposer = $nominee->proposer;
        $seconders = $nominee->seconders;

        /** @var Nomination $nomination */
        $nomination = $position->event;

        return (new MailMessage)
            ->subject(subject: "Nomination Proposal for $position->name - Action Required")
            ->greeting(greeting: "Dear $nominee->display_name,")
            ->line(line: "You have been nominated for the position of $position->name by $proposer->display_name. Congratulations on this nomination!")
            ->line(line: "To proceed further, we kindly request you to review the nomination and indicate your acceptance or decline of the proposal. Your response is crucial for the progression of the nomination process.")
            ->line(line: "Please click on the following button to access the nomination and respond accordingly.")
            ->action(text: "Click Here", url: url('/'))
            ->line(line: "If you have any questions or concerns, feel free to contact our support team.")
            ->line(line: "Thank you for your prompt attention to this matter. We appreciate your active participation in the nomination process.");
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
