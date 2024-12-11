<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    protected $fillable = [
        'name',
        'description',
        'timezone',
        'organisation_id',
    ];

    protected $casts = [
        'organisation_id' => 'int',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(related: Organisation::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(related: Participant::class);
    }

    public function resolutions(): HasMany
    {
        return $this->hasMany(related: Resolution::class);
    }
}
