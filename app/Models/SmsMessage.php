<?php

namespace App\Models;

use App\Models\Enums\SmsMessageProvider;
use App\Models\Enums\SmsMessagePurpose;
use App\Models\Enums\SmsMessageStatus;
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
}
