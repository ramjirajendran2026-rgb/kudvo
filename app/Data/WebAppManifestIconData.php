<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class WebAppManifestIconData extends Data
{
    public function __construct(
        public string $src,
        public string $type,
        public string $sizes,
        public ?string $purpose = null,
    ) { }
}
