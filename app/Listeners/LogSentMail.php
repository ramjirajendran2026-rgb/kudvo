<?php

namespace App\Listeners;

use Akhan619\LaravelSesEventManager\App\Models\Email;
use App\Actions\ParseMailMessageId;
use Illuminate\Mail\Events\MessageSent;

class LogSentMail
{
    public function handle(MessageSent $event): void
    {
        $result = $event->sent;

        $messageId = app(abstract: ParseMailMessageId::class)
            ->execute($event->sent);

        if (filled($messageId)) {
            \App\Models\Email::create([
                'message_id' => $messageId,
                'from_address' => current($result->getOriginalMessage()->getFrom())->getAddress(),
                'from_name' => current($result->getOriginalMessage()->getFrom())->getName(),
                'to_address' => current($result->getOriginalMessage()->getTo())->getAddress(),
                'to_name' => current($result->getOriginalMessage()->getTo())->getName(),
                'subject' => $result->getOriginalMessage()->getSubject(),
            ]);
        }
    }
}
