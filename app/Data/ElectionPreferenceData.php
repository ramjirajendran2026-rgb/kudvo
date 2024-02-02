<?php

namespace App\Data;

use App\Enums\CandidateSort;
use Spatie\LaravelData\Data;

class ElectionPreferenceData extends Data
{
    public function __construct(
        public bool $eul_mail = false,

        public bool $eul_sms = false,

        public bool $mfa_mail = false,

        public bool $mfa_sms = false,

        public bool $mfa_sms_auto_fill_only = false,

        public bool $voted_confirmation_mail = false,

        public bool $voted_confirmation_sms = false,

        public bool $voted_ballot_download = false,

        public bool $voted_ballot_mail = false,

        public bool $dnt_votes = false,

        public bool $voted_ballot_update = false,

        public ?int $ip_restriction_threshold = null,

        public CandidateSort $candidate_sort = CandidateSort::MANUAL,

        public bool $candidate_photo = false,

        public bool $candidate_bio = false,

        public bool $candidate_attachment = false,
    )
    {
    }
}
