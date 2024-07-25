<?php

namespace App\Data\Home;

use Spatie\LaravelData\Data;

class FeatureCardData extends Data
{
    public function __construct(
        public string $title,
        public string $image,
        public array $points = [],
    ) {}
}
