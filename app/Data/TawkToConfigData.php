<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class TawkToConfigData extends Data
{
    public function __construct(
        public bool $enabled = false,
        public bool $home_page = false,
        public bool $product_pages = false,
        public bool $wiki_pages = false,
        public bool $user_panel = false,
        public bool $election_panel = false,
        public bool $meeting_panel = false,
        public bool $nomination_panel = false,
        public ?string $script = null,
    ) {}
}
