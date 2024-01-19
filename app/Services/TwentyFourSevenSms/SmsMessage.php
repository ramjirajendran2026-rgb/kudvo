<?php

namespace App\Services\TwentyFourSevenSms;

use Illuminate\Support\Str;

class SmsMessage
{
    public function __construct(
        protected ?string $message = null,
        protected ServiceName $serviceName = ServiceName::TEMPLATE_BASED,
        protected ?string $senderId = null,
        protected ?bool $unicode = null,
    ) {
    }

    public function message(?string $value): static
    {
        $this->message = $value;

        return $this;
    }

    public function serviceName(ServiceName $value): static
    {
        $this->serviceName = $value;

        return $this;
    }

    public function senderId(string $value): static
    {
        $this->senderId = $value;

        return $this;
    }

    public function unicode(bool $value = true): static
    {
        $this->unicode = $value;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getServiceName(): ServiceName
    {
        return $this->serviceName;
    }

    public function getSenderId(): ?string
    {
        return $this->senderId;
    }

    public function isUnicode(): bool
    {
        return filled($this->unicode)
            ? $this->unicode
            : filled($this->getMessage()) && Str::isUnicode($this->getMessage());
    }

    public function toApi(): array
    {
        return [
            'SenderID' => $this->getSenderId(),
            'Message' => $this->getMessage(),
            'ServiceName' => $this->getServiceName()->name,
        ];
    }
}
