<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ballot extends Model
{
    protected $fillable = [
        'ip_address',
        'voted_at',
        'elector_id',
        'auth_session_id',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
        'elector_id' => 'int',
        'auth_session_id' => 'int',
    ];

    public function elector(): BelongsTo
    {
        return $this->belongsTo(related: Elector::class);
    }

    public function authSession(): BelongsTo
    {
        return $this->belongsTo(related: AuthSession::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(related: Vote::class);
    }

    public function isVoted(): bool
    {
        return filled($this->voted_at);
    }
}
