<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingPayment extends Model
{
    protected $fillable = [
        'currency',
        'base_fee',
        'participant_fee',
        'participant_count',
        'paid_at',
        'stripe_invoice_id',
        'stripe_invoice_data',
        'meeting_id',
    ];

    protected $casts = [
        'base_fee' => 'int',
        'participant_fee' => 'int',
        'participant_count' => 'int',
        'paid_at' => 'immutable_datetime',
        'stripe_invoice_data' => 'array',
        'meeting_id' => 'int',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }
}
