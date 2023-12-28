<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    protected $fillable = [
        'content',
        'ballot_id',
        'position_id',
    ];

    protected $casts = [
        'content' => 'encrypted:array',
        'ballot_id' => 'int',
        'position_id' => 'int',
    ];

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(related: Ballot::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(related: Position::class);
    }
}
