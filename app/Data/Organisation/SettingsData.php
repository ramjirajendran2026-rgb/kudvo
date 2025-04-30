<?php

namespace App\Data\Organisation;

use Spatie\LaravelData\Data;

class SettingsData extends Data
{
    public function __construct(
        public bool $allow_branches = false,
        public bool $allow_members = false,
        public bool $allow_elections = true,
        public bool $allow_nominations = true,
        public bool $allow_meetings = true,
        public bool $allow_surveys = true,
    ) {}
}
