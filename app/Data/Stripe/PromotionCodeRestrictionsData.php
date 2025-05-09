<?php

namespace App\Data\Stripe;

use Spatie\LaravelData\Data;

class PromotionCodeRestrictionsData extends Data
{
    public function __construct(
        public ?array $currency_options,
        public bool $first_time_transaction,
        public ?int $minimum_amount,
        public ?string $minimum_amount_currency,
    ) {}
}
