<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ElectionResultMetaData extends Data
{
    public function __construct(
        public string $key,
        public int $value,
    )
    {
    }
}
