<?php

namespace App\Data\Home;

use Spatie\LaravelData\Data;

class ProductCardData extends Data
{
    public function __construct(
        public string $title,
        public string $description,
        public ?string $title_color = null,
        public ?string $cta_label = null,
        public ?string $cta_url = null,
    ) {
    }
}
