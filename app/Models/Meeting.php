<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    protected $fillable = [
        'title',
        'timezone',
        'voting_starts_at',
        'voting_ends_at',
        'organisation_id',
    ];

    protected $casts = [
        'voting_starts_at' => 'datetime',
        'voting_ends_at' => 'datetime',
        'organisation_id' => 'int',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(related: Organisation::class);
    }

    public function resolutions(): HasMany
    {
        return $this->hasMany(related: Resolution::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(related: Participant::class);
    }
}
