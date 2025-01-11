<?php

namespace App\Models;

use App\Enums\ResolutionChoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResolutionVote extends Model
{
    protected $fillable = [
        'response',
        'participant_id',
        'resolution_id',
    ];

    protected $casts = [
        'response' => ResolutionChoice::class,
        'participant_id' => 'int',
        'resolution_id' => 'int',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(related: Participant::class);
    }

    public function resolution(): BelongsTo
    {
        return $this->belongsTo(related: Resolution::class);
    }
}
