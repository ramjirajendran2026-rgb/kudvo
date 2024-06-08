<?php

namespace App\Data\Election;

use Spatie\LaravelData\Data;

class BoothPreferenceData extends Data
{
    public function __construct(
        public bool $login_by_admin = false,
        public bool $login_by_self = true,
        public bool $logout_by_admin = false,
        public bool $logout_by_self = true,
        public bool $voted_ballot_print_by_admin = false,
        public bool $voted_ballot_print_by_self = false,
        public bool $flash_voted_ballot = false,
        public ?int $after_vote_session_timeout = null
    ) {
    }
}
