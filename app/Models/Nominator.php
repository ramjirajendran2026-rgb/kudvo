<?php

namespace App\Models;

use App\Enums\NominatorStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nominator extends Model
{
    protected $fillable = [
        'membership_number',
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'status',
        'decided_at',
        'nominee_id',
        'elector_id',
    ];

    protected $casts = [
        'status' => NominatorStatus::class,
        'decided_at' => 'datetime',
        'nominee_id' => 'int',
        'elector_id' => 'int',
    ];

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->membership_number.
                (filled(value: $this->full_name) ? ' ('.$this->full_name.')' : ''),
        );
    }

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(related: Nominee::class);
    }

    public function elector(): BelongsTo
    {
        return $this->belongsTo(related: Elector::class);
    }

    public function isAccepted(): bool
    {
        return $this->status == NominatorStatusEnum::ACCEPTED;
    }

    public function isPending(): bool
    {
        return $this->status == NominatorStatusEnum::PENDING;
    }

    public function isDeclined(): bool
    {
        return $this->status == NominatorStatusEnum::DECLINED;
    }
}
