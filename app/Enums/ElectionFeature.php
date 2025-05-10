<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

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
    case AnonymousVotes = 'trackable_votes';
    case ElectorEmailUnique = 'elector_email_unique';
    case ElectorPhoneUnique = 'elector_phone_unique';
    case ElectorCorrections = 'elector_corrections';
    case CandidateShuffling = 'candidate_shuffling';
    case CandidateSorting = 'candidate_sorting';
    case CandidatePhoto = 'candidate_photo';
    case CandidateSymbol = 'candidate_symbol';
    case CandidateTeam = 'candidate_team';
    case RestrictSelfVote = 'restrict_self_vote';
    case DisableUnopposedSelection = 'disable_unopposed_selection';
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
            self::AnonymousVotes => 'Anonymous votes',
            self::ElectorEmailUnique => 'Unique email check for elector',
            self::ElectorPhoneUnique => 'Unique phone check for elector',
            self::ElectorCorrections => 'Elector corrections',
            self::CandidateShuffling => 'Candidate shuffling',
            self::CandidateSorting => 'Candidate sorting',
            self::CandidatePhoto => 'Candidate photo',
            self::CandidateSymbol => 'Candidate symbol',
            self::CandidateTeam => 'Candidate team',
            self::RestrictSelfVote => 'Restrict self selection',
            self::DisableUnopposedSelection => 'Restrict unopposed candidate selection',
            self::SegmentedVoting => 'Segmented voting',
            self::BoothVoting => 'Booth voting',
        };
    }

    public function getShortLabel(): string
    {
        return match ($this) {
            self::BallotAccessCommonLink => 'Common link',
            self::BallotAccessUniqueLink => 'Unique link',
            self::BallotLinkEmail => 'Email link',
            self::BallotLinkSms => 'SMS link',
            self::BallotLinkWhatsapp => 'WhatsApp link',
            self::VerificationCodeEmail => 'Email code',
            self::VerificationCodeSms => 'SMS code',
            self::VerificationCodeWhatsapp => 'WhatsApp code',
            self::BallotAcknowledgementEmail => 'Email confirmation',
            self::BallotAcknowledgementSms => 'SMS confirmation',
            self::BallotAcknowledgementWhatsapp => 'WhatsApp confirmation',
            self::BallotCopyDownload => 'Download ballot',
            self::BallotCopyEmail => 'Email ballot',
            self::BallotCopyWhatsapp => 'WhatsApp ballot',
            self::IpRestriction => 'IP restriction',
            self::DeviceRestriction => 'Device restriction',
            self::EditableVotes => 'Editable votes',
            self::AnonymousVotes => 'Anonymous votes',
            self::ElectorEmailUnique => 'Unique email check',
            self::ElectorPhoneUnique => 'Unique phone check',
            self::ElectorCorrections => 'Elector corrections',
            self::CandidateShuffling => 'Candidate shuffling',
            self::CandidateSorting => 'Candidate sorting',
            self::CandidatePhoto => 'Candidate photo',
            self::CandidateSymbol => 'Candidate symbol',
            self::CandidateTeam => 'Candidate team',
            self::RestrictSelfVote => 'Restrict self selection',
            self::DisableUnopposedSelection => 'Restrict unopposed candidate selection',
            self::SegmentedVoting => 'Segmented voting',
            self::BoothVoting => 'Booth voting',
        };
    }

    public function getPreferenceKey(): ?string
    {
        return match ($this) {
            self::BallotAccessCommonLink => 'ballot_link_common',
            self::BallotAccessUniqueLink => 'ballot_link_unique',
            self::BallotLinkEmail => 'ballot_link_mail',
            self::BallotLinkSms => 'ballot_link_sms',
            self::BallotLinkWhatsapp => 'ballot_link_whatsapp',
            self::VerificationCodeEmail => 'mfa_mail',
            self::VerificationCodeSms => 'mfa_sms',
            self::VerificationCodeWhatsapp => 'mfa_whatsapp',
            self::BallotAcknowledgementEmail => 'voted_confirmation_mail',
            self::BallotAcknowledgementSms => 'voted_confirmation_sms',
            self::BallotAcknowledgementWhatsapp => 'voted_confirmation_whatsapp',
            self::BallotCopyDownload => 'voted_ballot_download',
            self::BallotCopyEmail => 'voted_ballot_mail',
            self::BallotCopyWhatsapp => 'voted_ballot_whatsapp',
            self::IpRestriction => 'ip_restriction_threshold',
            self::DeviceRestriction => 'prevent_duplicate_device',
            self::EditableVotes => 'voted_ballot_update',
            self::AnonymousVotes => 'dnt_votes',
            self::ElectorEmailUnique => 'elector_duplicate_email',
            self::ElectorPhoneUnique => 'elector_duplicate_phone',
            self::ElectorCorrections => 'elector_update_after_publish',
            self::CandidatePhoto => 'candidate_photo',
            self::CandidateSymbol => 'candidate_symbol',
            self::CandidateTeam => 'candidate_group',
            self::RestrictSelfVote => 'restrict_self_vote',
            self::DisableUnopposedSelection => 'disable_unopposed_selection',
            self::SegmentedVoting => 'segmented_ballot',
            self::BoothVoting => 'booth_voting',
            default => null,
        };
    }

    public function isSmsFeature(): bool
    {
        return match ($this) {
            self::BallotLinkSms,
            self::VerificationCodeSms,
            self::BallotAcknowledgementSms => true,
            default => false,
        };
    }

    public function hasMaxUsage(): bool
    {
        return match ($this) {
            self::BallotLinkSms,
            self::VerificationCodeSms,
            self::BallotAcknowledgementSms => true,
            default => false,
        };
    }
}
