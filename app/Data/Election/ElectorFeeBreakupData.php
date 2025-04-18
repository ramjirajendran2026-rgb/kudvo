<?php

namespace App\Data\Election;

use Illuminate\Support\Traits\Conditionable;
use Spatie\LaravelData\Data;

class ElectorFeeBreakupData extends Data
{
    use Conditionable;

    public function __construct(
        public int $base_fee = 0,
        public int $ballot_link_common = 0,
        public int $ballot_link_unique = 0,
        public int $ballot_link_mail = 0,
        public int $ballot_link_sms = 0,
        public int $ballot_link_whatsapp = 0,
        public int $mfa_mail = 0,
        public int $mfa_sms = 0,
        public int $mfa_sms_auto_fill_only = 0,
        public int $mfa_whatsapp = 0,
        public int $voted_confirmation_mail = 0,
        public int $voted_confirmation_sms = 0,
        public int $voted_confirmation_whatsapp = 0,
        public int $voted_ballot_download = 0,
        public int $voted_ballot_mail = 0,
        public int $voted_ballot_whatsapp = 0,
        public int $dnt_votes = 0,
        public int $voted_ballot_update = 0,
        public int $prevent_duplicate_device = 0,
        public int $ip_restriction = 0,
        public int $elector_duplicate_email = 0,
        public int $elector_duplicate_phone = 0,
        public int $elector_update_after_publish = 0,
        public int $candidate_sort_random = 0,
        public int $candidate_photo = 0,
        public int $candidate_symbol = 0,
        public int $candidate_bio = 0,
        public int $candidate_attachment = 0,
        public int $candidate_group = 0,
        public int $booth_voting = 0,
    ) {}

    public function ballotLinkCommon(int $fee): static
    {
        $this->ballot_link_common = $fee;

        return $this;
    }

    public function ballotLinkUnique(int $fee): static
    {
        $this->ballot_link_unique = $fee;

        return $this;
    }

    public function ballotLinkMail(int $fee): static
    {
        $this->ballot_link_mail = $fee;

        return $this;
    }

    public function ballotLinkSms(int $fee): static
    {
        $this->ballot_link_sms = $fee;

        return $this;
    }

    public function ballotLinkWhatsapp(int $fee): static
    {
        $this->ballot_link_whatsapp = $fee;

        return $this;
    }

    public function mfaMail(int $fee): static
    {
        $this->mfa_mail = $fee;

        return $this;
    }

    public function mfaSms(int $fee): static
    {
        $this->mfa_sms = $fee;

        return $this;
    }

    public function mfaSmsAutoFillOnly(int $fee): static
    {
        $this->mfa_sms_auto_fill_only = $fee;

        return $this;
    }

    public function mfaWhatsapp(int $fee): static
    {
        $this->mfa_whatsapp = $fee;

        return $this;
    }

    public function votedConfirmationMail(int $fee): static
    {
        $this->voted_confirmation_mail = $fee;

        return $this;
    }

    public function votedConfirmationSms(int $fee): static
    {
        $this->voted_confirmation_sms = $fee;

        return $this;
    }

    public function votedConfirmationWhatsapp(int $fee): static
    {
        $this->voted_confirmation_whatsapp = $fee;

        return $this;
    }

    public function votedBallotDownload(int $fee): static
    {
        $this->voted_ballot_download = $fee;

        return $this;
    }

    public function votedBallotMail(int $fee): static
    {
        $this->voted_ballot_mail = $fee;

        return $this;
    }

    public function votedBallotWhatsapp(int $fee): static
    {
        $this->voted_ballot_whatsapp = $fee;

        return $this;
    }

    public function dntVotes(int $fee): static
    {
        $this->dnt_votes = $fee;

        return $this;
    }

    public function votedBallotUpdate(int $fee): static
    {
        $this->voted_ballot_update = $fee;

        return $this;
    }

    public function preventDuplicateDevice(int $fee): static
    {
        $this->prevent_duplicate_device = $fee;

        return $this;
    }

    public function ipRestriction(int $fee): static
    {
        $this->ip_restriction = $fee;

        return $this;
    }

    public function electorDuplicateEmail(int $fee): static
    {
        $this->elector_duplicate_email = $fee;

        return $this;
    }

    public function electorDuplicatePhone(int $fee): static
    {
        $this->elector_duplicate_phone = $fee;

        return $this;
    }

    public function electorUpdateAfterPublish(int $fee): static
    {
        $this->elector_update_after_publish = $fee;

        return $this;
    }

    public function candidateSortRandom(int $fee): static
    {
        $this->candidate_sort_random = $fee;

        return $this;
    }

    public function candidatePhoto(int $fee): static
    {
        $this->candidate_photo = $fee;

        return $this;
    }

    public function candidateSymbol(int $fee): static
    {
        $this->candidate_symbol = $fee;

        return $this;
    }

    public function candidateBio(int $fee): static
    {
        $this->candidate_bio = $fee;

        return $this;
    }

    public function candidateAttachment(int $fee): static
    {
        $this->candidate_attachment = $fee;

        return $this;
    }

    public function candidateGroup(int $fee): static
    {
        $this->candidate_group = $fee;

        return $this;
    }

    public function boothVoting(int $fee): static
    {
        $this->booth_voting = $fee;

        return $this;
    }
}
