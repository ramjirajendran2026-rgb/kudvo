<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class NominationPreferenceData extends Data
{
    public function __construct(
        public bool $mfa_mail = false,

        public bool $mfa_sms = false,

        public bool $candidate_photo = false,

        public bool $candidate_bio = false,

        public bool $candidate_attachment = false,
    )
    {
    }
}
