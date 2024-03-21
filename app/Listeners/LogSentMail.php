<?php

namespace App\Listeners;

use Akhan619\LaravelSesEventManager\App\Models\Email;
use Illuminate\Mail\Events\MessageSent;

class LogSentMail
{
    public function handle(MessageSent $event): void
    {
        $result = $event->sent;

        Email::create([
            'message_id'        => $result->getOriginalMessage()->getHeaders()->get('X-SES-Message-ID')->getValue(),
            'email'             => current($result->getOriginalMessage()->getTo())->getAddress(),
            'name'              => current($result->getOriginalMessage()->getTo())->getName(),
            'subject'           => $result->getOriginalMessage()->getSubject(),
        ]);
    }
}
