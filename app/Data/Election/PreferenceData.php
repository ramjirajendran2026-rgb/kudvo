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
        public bool $skip_ballot_selection_confirmation = false,
        public bool $segmented_ballot = false,
        public bool $waterfall_voting = false,
        public bool $booth_voting = false,
        public ?WebAppManifestData $web_app_manifest = null,
        public int $candidate_per_row = 2,
    ) {}

    public function isBallotLinkBlastNeeded(?Elector $elector = null): bool
    {
        if (blank($elector)) {
            return $this->ballot_link_mail || $this->ballot_link_sms || $this->ballot_link_whatsapp;
        }

        return (filled($elector?->email) && $this->ballot_link_mail) ||
            (filled($elector?->phone) && $this->ballot_link_sms) ||
            (filled($elector?->phone) && $this->ballot_link_whatsapp);
    }

    public function isBallotConfirmationNeeded(?Elector $elector = null): bool
    {
        if (blank($elector)) {
            return $this->voted_confirmation_mail || $this->voted_confirmation_sms || $this->voted_confirmation_whatsapp;
        }

        if (! $elector->ballot?->isVoted()) {
            return false;
        }

        return (filled($elector?->email) && $this->voted_confirmation_mail) ||
            (filled($elector?->phone) && $this->voted_confirmation_sms) ||
            (filled($elector?->phone) && $this->voted_confirmation_whatsapp);
    }
}
