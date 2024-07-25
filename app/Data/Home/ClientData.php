<?php

namespace App\Data\Home;

use Spatie\LaravelData\Data;

class ClientData extends Data
{
    public function __construct(
        public string $name,
        public string $logo,
    ) {}
}
