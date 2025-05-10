<?php

namespace App\Data\Election;

use App\Data\WebAppManifestData;
use App\Enums\CandidateSort;
use App\Models\Elector;
use Spatie\LaravelData\Data;

class PreferenceData extends Data
{
    public function __construct(
        public bool $ballot_link_common = false,
        public bool $ballot_link_unique = true,
        public bool $ballot_link_mail = true,
        public bool $ballot_link_sms = false,
        public bool $ballot_link_whatsapp = false,
        public bool $mfa_mail = false,
        public bool $mfa_sms = true,
        public bool $mfa_sms_auto_fill_only = false,
        public bool $mfa_whatsapp = false,
        public bool $voted_confirmation_mail = true,
        public bool $voted_confirmation_sms = false,
        public bool $voted_confirmation_whatsapp = false,
        public bool $voted_ballot_download = false,
        public bool $voted_ballot_mail = false,
        public bool $voted_ballot_whatsapp = false,
        public bool $dnt_votes = true,
        public bool $voted_ballot_update = false,
        public bool $prevent_duplicate_device = false,
        public ?int $ip_restriction_threshold = null,
        public bool $elector_duplicate_email = true,
        public bool $elector_duplicate_phone = true,
        public bool $elector_update_after_publish = false,
        public CandidateSort $candidate_sort = CandidateSort::MANUAL,
        public bool $candidate_photo = false,
        public bool $candidate_symbol = false,
        public bool $candidate_bio = false,
        public bool $candidate_attachment = false,
        public bool $candidate_group = false,
        public bool $restrict_self_vote = false,
        public bool $disable_unopposed_selection = false,
        public bool $segmented_ballot = false,
        public bool $booth_voting = false,
        public ?WebAppManifestData $web_app_manifest = null,
    ) {}

    public function isBallotLinkBlastNeeded(?Elector $elector = null): bool
    {
        if (blank($elector)) {
            return $this->ballot_link_mail || $this->ballot_link_sms;
        }

        return (filled($elector?->email) && $this->ballot_link_mail) ||
            (filled($elector?->phone) && $this->ballot_link_sms);
    }
}
