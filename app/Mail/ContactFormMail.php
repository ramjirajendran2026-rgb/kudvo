<?php

namespace App\Mail;

use App\Data\ContactFormData;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactFormData $data)
    {
    }

    public function envelope(): Envelope
    {
        return (new Envelope(
            subject: 'New contact form submission',
        ))->replyTo(address: new Address(address: $this->data->email, name: $this->data->name));
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-form',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
