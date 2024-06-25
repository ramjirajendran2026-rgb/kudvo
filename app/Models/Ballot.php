<?php

namespace App\Models;

use App\Enums\BallotType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ballot extends Model
{
    protected $fillable = [
        'type',
        'ip_address',
        'voted_at',
        'mock',
        'booth_id',
        'elector_id',
        'auth_session_id',
    ];

    protected $casts = [
        'type' => BallotType::class,
        'voted_at' => 'datetime',
        'mock' => 'bool',
        'booth_id' => 'int',
        'elector_id' => 'int',
        'auth_session_id' => 'int',
    ];

    public function booth(): BelongsTo
    {
        return $this->belongsTo(related: ElectionBoothToken::class);
    }

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

    public function scopeMock(Builder $query): Builder
    {
        return $query->where('mock', true);
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('mock', false);
    }

    public function scopeVoted(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'voted_at');
    }

    public function scopeNonVoted(Builder $query): Builder
    {
        return $query->whereNull(columns: 'voted_at');
    }

    public function scopeBooth(Builder $query): Builder
    {
        return $query->where('type', BallotType::Booth);
    }

    public function scopeDirect(Builder $query): Builder
    {
        return $query->where('type', BallotType::Direct);
    }

    public function isVoted(): bool
    {
        return filled($this->voted_at);
    }
}
