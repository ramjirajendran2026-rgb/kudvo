<?php

namespace App\Enums;

enum SmsMessagePurpose: string
{
    case BallotLink = 'ballot_link';

    case BallotMfaCode = 'ballot_mfa_code';

    case VotedConfirmation = 'voted_confirmation';
}
