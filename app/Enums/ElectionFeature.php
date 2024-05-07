<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ElectionFeature: string implements HasLabel
{
    case BallotAccessCommonLink = 'ballot_access_common_link';
    case BallotAccessUniqueLink = 'ballot_access_unique_link';
    case BallotLinkEmail = 'ballot_link_email';
    case BallotLinkSms = 'ballot_link_sms';
    case BallotLinkWhatsapp = 'ballot_link_whatsapp';
    case VerificationCodeEmail = 'verification_code_email';
    case VerificationCodeSms = 'verification_code_sms';
    case VerificationCodeWhatsapp = 'verification_code_whatsapp';
    case BallotAcknowledgementEmail = 'ballot_acknowledgement_email';
    case BallotAcknowledgementSms = 'ballot_acknowledgement_sms';
    case BallotAcknowledgementWhatsapp = 'ballot_acknowledgement_whatsapp';
    case BallotCopyDownload = 'ballot_copy_download';
    case BallotCopyEmail = 'ballot_copy_email';
    case BallotCopyWhatsapp = 'ballot_copy_whatsapp';
    case IpRestriction = 'ip_restriction';
    case DeviceRestriction = 'device_restriction';
    case EditableVotes = 'editable_votes';
    case TrackableVotes = 'trackable_votes';
    case ElectorEmailUnique = 'elector_email_unique';
    case ElectorPhoneUnique = 'elector_phone_unique';
    case ElectorCorrections = 'elector_corrections';
    case CandidateShuffling = 'candidate_shuffling';
    case CandidateSorting = 'candidate_sorting';
    case CandidatePhoto = 'candidate_photo';
    case CandidateSymbol = 'candidate_symbol';
    case CandidateTeam = 'candidate_team';
    case SegmentedVoting = 'segmented_voting';
    case BoothVoting = 'booth_voting';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BallotAccessCommonLink => 'Ballot access via common link',
            self::BallotAccessUniqueLink => 'Ballot access via unique link',
            self::BallotLinkEmail => 'Voting link via email',
            self::BallotLinkSms => 'Voting link via SMS',
            self::BallotLinkWhatsapp => 'Voting link via WhatsApp',
            self::VerificationCodeEmail => 'Verification code via email',
            self::VerificationCodeSms => 'Verification code via SMS',
            self::VerificationCodeWhatsapp => 'Verification code via WhatsApp',
            self::BallotAcknowledgementEmail => 'Voted acknowledgement via email',
            self::BallotAcknowledgementSms => 'Voted acknowledgement via SMS',
            self::BallotAcknowledgementWhatsapp => 'Voted acknowledgement via WhatsApp',
            self::BallotCopyDownload => 'Voted ballot copy download',
            self::BallotCopyEmail => 'Voted ballot copy via email',
            self::BallotCopyWhatsapp => 'Voted ballot copy via WhatsApp',
            self::IpRestriction => 'IP restriction',
            self::DeviceRestriction => 'Device restriction',
            self::EditableVotes => 'Editable votes',
            self::TrackableVotes => 'Trackable votes',
            self::ElectorEmailUnique => 'Unique email check for elector',
            self::ElectorPhoneUnique => 'Unique phone check for elector',
            self::ElectorCorrections => 'Elector corrections',
            self::CandidateShuffling => 'Candidate shuffling',
            self::CandidateSorting => 'Candidate sorting',
            self::CandidatePhoto => 'Candidate photo',
            self::CandidateSymbol => 'Candidate symbol',
            self::CandidateTeam => 'Candidate team',
            self::SegmentedVoting => 'Segmented voting',
            self::BoothVoting => 'Booth voting',
        };
    }
}
