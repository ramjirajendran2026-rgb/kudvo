<?php

namespace App\Actions\Election;

use App\Enums\OneTimePasswordPurpose;
use App\Facades\Kudvo;
use App\Models\Election;
use App\Models\Elector;
use App\Models\OneTimePassword;
use App\Notifications\Election\MfaCodeNotification;

class SendElectorMfaCode
{
    public function execute(Elector $elector, ?Election $election = null): ?OneTimePassword
    {
        $election ??= Kudvo::getElection();

        if (blank($election)) {
            return null;
        }

        $oneTimePassword = $elector
            ->oneTimePasswords()
            ->create(attributes: [
                'purpose' => OneTimePasswordPurpose::MFA,

                ...($election->preference->mfa_sms || $election->preference->mfa_whatsapp) ? ['phone' => $elector->phone] : [],
                ...$election->preference->mfa_mail ? ['email' => $elector->email] : [],
            ]);

        $oneTimePassword->send(
            notification: new MfaCodeNotification(
                election: $election,
                oneTimePassword: $oneTimePassword,
            )
        );

        return $oneTimePassword;
    }
}
