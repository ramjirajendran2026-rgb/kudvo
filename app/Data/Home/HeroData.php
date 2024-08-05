<?php

namespace App\Data\Home;

use Spatie\LaravelData\Data;

class HeroData extends Data
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $image_alt = null,
        public ?string $cta_label = null,
        public ?string $cta_url = null,
        public ?string $cta2_label = null,
        public ?string $cta2_url = null,
    ) {}
}
