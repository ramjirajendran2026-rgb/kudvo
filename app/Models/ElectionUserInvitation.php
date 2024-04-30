<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ElectionUserInvitation extends Model
{
    use HasUlids;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'email',
        'token',
        'designation',
        'permissions',
        'accepted_at',
        'election_id',
        'user_id',
        'invitor_id',
    ];

    protected $casts = [
        'permissions' => 'array',
        'accepted_at' => 'datetime',
        'election_id' => 'int',
        'user_id' => 'int',
        'invitor_id' => 'int',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(related: Election::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }

    public function invitor(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->whereNotNull('accepted_at');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('accepted_at');
    }

    public function getRouteKeyName(): string
    {
        return 'token';
    }

    public function uniqueIds(): array
    {
        return ['token'];
    }
}
