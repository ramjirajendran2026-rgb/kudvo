<?php

namespace App\Models;

use App\Enums\SmsMessagePurpose;
use App\Enums\WhatsAppMessageStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WhatsAppMessage extends Model
{
    protected $fillable = [
        'purpose',
        'phone',
        'status',
        'notes',
        'message_id',
        'message_status',
        'message_type',
        'message_meta',
        'whatsappable_id',
        'whatsappable_type',
    ];

    protected $casts = [
        'purpose' => SmsMessagePurpose::class,
        'status' => WhatsAppMessageStatus::class,
        'message_meta' => 'array',
        'whatsappable_id' => 'int',
    ];

    public function whatsappable(): MorphTo
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
