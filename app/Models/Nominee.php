<?php

namespace App\Models;

use App\Enums\NomineeStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nominee extends Model
{
    protected $fillable = [
        'membership_number',
        'title',
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'self_nomination',
        'status',
        'decided_at',
        'scrutinised_at',
        'withdrawn_at',
        'position_id',
        'elector_id',
        'scrutiniser_id',
    ];

    protected $casts = [
        'self_nomination' => 'bool',
        'status' => NomineeStatusEnum::class,
        'decided_at' => 'datetime',
        'scrutinised_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'position_id' => 'int',
        'elector_id' => 'int',
        'scrutiniser_id' => 'int',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(related: Position::class);
    }

    public function elector(): BelongsTo
    {
        return $this->belongsTo(related: Elector::class);
    }

    public function scrutiniser(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }
}
