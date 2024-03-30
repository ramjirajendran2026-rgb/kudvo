<?php

namespace App\Models;

use App\Enums\SmsMessageProvider;
use App\Enums\SmsMessagePurpose;
use App\Enums\SmsMessageStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SmsMessage extends Model
{
    protected $fillable = [
        'purpose',
        'phone',
        'status',
        'notes',
        'provider',
        'provider_message_id',
        'provider_status',
        'provider_meta',
        'smsable_id',
        'smsable_type',
    ];

    protected $casts = [
        'purpose' => SmsMessagePurpose::class,
        'status' => SmsMessageStatus::class,
        'provider' => SmsMessageProvider::class,
        'provider_meta' => 'encrypted:array',
        'smsable_id' => 'int',
    ];

    public function smsable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeBallotLink(Builder $query): Builder
    {
        return $query->where('purpose', SmsMessagePurpose::BallotLink);
    }

    public function scopeBallotMfaCode(Builder $query): Builder
    {
        return $query->where('purpose', SmsMessagePurpose::BallotMfaCode);
    }

    public function scopeVotedConfirmation(Builder $query): Builder
    {
        return $query->where('purpose', SmsMessagePurpose::VotedConfirmation);
    }
}
