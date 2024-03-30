<?php

namespace App\Enums;

enum MailMessagePurpose: string
{
    case BallotLink = 'ballot_link';

    case BallotMfaCode = 'ballot_mfa_code';

    case VotedConfirmation = 'voted_confirmation';

    case VotedBallotCopy = 'voted_ballot_copy';
}
