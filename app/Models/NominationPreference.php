<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NominationPreference extends Model
{
    protected $fillable = [
        'mfa_mail',
        'mfa_sms',
        'nomination_id',
    ];

    protected $casts = [
        'mfa_mail' => 'bool',
        'mfa_sms' => 'bool',
        'nomination_id' => 'int',
    ];

    public function nomination(): BelongsTo
    {
        return $this->belongsTo(related: Nomination::class);
    }

    public function isMfaRequired(): bool
    {
        return $this->mfa_mail || $this->mfa_sms;
    }
}
