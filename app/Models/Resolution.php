<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resolution extends Model
{
    protected $fillable = [
        'overview',
        'meeting_id',
    ];

    protected $casts = [
        'meeting_id' => 'int',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(related: Meeting::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(related: ResolutionVote::class);
    }
}
