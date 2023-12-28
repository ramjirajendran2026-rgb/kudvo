<?php

namespace App\Models;

use App\Console\NominatorStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nominator extends Model
{
    protected $fillable = [
        'membership_number',
        'title',
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'status',
        'decided_at',
        'nominee_id',
        'elector_id',
    ];

    protected $casts = [
        'status' => NominatorStatusEnum::class,
        'decided_at' => 'datetime',
        'nominee_id' => 'int',
        'elector_id' => 'int',
    ];

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(related: Nominee::class);
    }

    public function elector(): BelongsTo
    {
        return $this->belongsTo(related: Elector::class);
    }
}
