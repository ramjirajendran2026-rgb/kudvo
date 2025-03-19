<?php

namespace App\Data\Survey;

use Spatie\LaravelData\Data;

class SettingsData extends Data
{
    public function __construct(
        public ?string $reference_number_prefix = null,
        public int $reference_number_pad_length = 1,
    ) {}
}
