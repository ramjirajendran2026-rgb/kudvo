<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectionMonitorToken extends Model
{
    use HasUuids;

    protected $fillable = [
        'key',
        'activated_at',
        'ip_address',
        'user_agent',
        'election_id',
    ];

    protected $casts = [
        'election_id' => 'int',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(related: Election::class);
    }

    public function scopeActivated(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'activated_at');
    }

    public function uniqueIds(): array
    {
        return ['key'];
    }

    public function isActivated(): bool
    {
        return filled($this->activated_at);
    }
}
