<?php

namespace App\Notifications\Election;

use App\Enums\MailMessagePurpose;
use App\Models\ElectionUserInvitation;
use App\Notifications\Contracts\HasMailMessagePurpose;
use Filament\Facades\Filament;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class CollaboratorInvitationNotification extends Notification implements HasMailMessagePurpose
{
    public function __construct(
        public ElectionUserInvitation $invitation,
    )
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(subject: 'Election Collaboration Invitation')
            ->greeting(greeting: 'Hello!')
            ->line(line: "You've been invited to collaborate on the election **{$this->invitation->election->name}** as **{$this->invitation->designation}**.")
            ->action('Accept Invitation', URL::signedRoute(name: 'filament.user.election-collaborators.accept', parameters: ['invitation' => $this->invitation->getRouteKey()]));
    }

    public function toArray($notifiable): array
    {
        return [];
    }

    public function getMailMessagePurpose(object $notifiable): MailMessagePurpose
    {
        return MailMessagePurpose::ElectionCollaboratorInvitation;
    }
}
