<?php

namespace App\Data\Election;

use Spatie\LaravelData\Data;

class ResultMetaData extends Data
{
    public function __construct(
        public string $key,
        public int $value,
    ) {
    }
}
