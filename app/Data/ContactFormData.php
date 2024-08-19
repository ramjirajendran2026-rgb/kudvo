<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ContactFormData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $message = null,
    ) {}
}
