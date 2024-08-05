<?php

namespace App\Data\Home;

use Spatie\LaravelData\Data;

class FeatureCardData extends Data
{
    public function __construct(
        public string $title,
        public string $image,
        public string $image_alt,
        public array $points = [],
    ) {}
}
