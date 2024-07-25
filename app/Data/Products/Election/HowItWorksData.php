<?php

namespace App\Data\Products\Election;

use Spatie\LaravelData\Data;

class HowItWorksData extends Data
{
    public function __construct(
        public string $title,
        public string $description,
        public string $icon,
    ) {}
}
