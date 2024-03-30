<?php

namespace App\Actions;

use Illuminate\Mail\SentMessage;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\UnstructuredHeader;
use Symfony\Component\Mime\RawMessage;

class ParseMailMessageId
{
    public function execute(RawMessage|SentMessage $message): ?string
    {
        if ($message instanceof SentMessage) {
            $message = $message->getOriginalMessage();
        }

        if (! $message instanceof Email) {
            return null;
        }

        $messageHeader = $message->getHeaders()->get('X-SES-Message-ID')
            ?: $message->getHeaders()->get('X-Message-ID');

        if (! $messageHeader instanceof UnstructuredHeader) {
            return null;
        }

        return $messageHeader->getValue();
    }
}
