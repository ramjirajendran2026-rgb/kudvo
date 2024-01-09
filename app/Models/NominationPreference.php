<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NominationPreference extends Model
{
    protected $fillable = [
        'mfa_mail',
        'mfa_sms',
        'candidate_photo',
        'candidate_bio',
        'candidate_attachment',
        'nomination_id',
    ];

    protected $casts = [
        'mfa_mail' => 'bool',
        'mfa_sms' => 'bool',
        'candidate_photo' => 'bool',
        'candidate_bio' => 'bool',
        'candidate_attachment' => 'bool',
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
