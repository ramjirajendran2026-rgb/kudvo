<?php

namespace App\Data\Election;

use App\Enums\ElectionFeature;
use Spatie\LaravelData\Data;

class PlanFeatureData extends Data
{
    public function __construct(
        public ElectionFeature $feature,
        public bool $is_add_on = false,
        public int $featureFee = 0,
        public int $electorFee = 0,
    )
    {
    }
}
