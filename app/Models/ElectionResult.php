<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectionResult extends Model
{
    protected $fillable = [
        'total_votes',
        'processed_votes',
        'completed_at',
        'content',
        'election_id',
    ];

    protected $casts = [
        'total_votes' => 'int',
        'processed_votes' => 'int',
        'completed_at' => 'datetime',
        'content' => 'array',
        'election_id' => 'int',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(related: Election::class);
    }
}
