<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ballot extends Model
{
    protected $fillable = [
        'ip_address',
        'voted_at',
        'elector_id',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
        'elector_id' => 'int',
    ];

    public function elector(): BelongsTo
    {
        return $this->belongsTo(related: Elector::class);
    }
}
