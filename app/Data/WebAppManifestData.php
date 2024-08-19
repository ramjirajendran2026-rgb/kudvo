<?php

namespace App\Data;

use App\Enums\WebAppManifestDisplay;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class WebAppManifestData extends Data
{
    public function __construct(
        public string $name,
        public ?string $short_name = null,
        #[DataCollectionOf(WebAppManifestIconData::class)]
        public ?DataCollection $icons = null,
        public ?string $start_url = null,
        public WebAppManifestDisplay $display = WebAppManifestDisplay::MinimalUi,
        public ?string $id = null,
    ) {}
}
