<?php

namespace App\Data\Election;

use App\Enums\ElectionFeature;
use Spatie\LaravelData\Data;

class PlanFeatureData extends Data
{
    public function __construct(
        public ElectionFeature $feature,
        public bool $is_add_on = false,
        public ?int $feature_fee = 0,
        public ?int $elector_fee = 0,
        public bool $show_in_pricing = false,
        public ?int $max_usage = null,
    ) {}
}
