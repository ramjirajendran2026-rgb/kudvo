<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'session_id',
        'guard_name',
        'ip_address',
        'user_agent',
        'last_activity_at',
        'authenticatable_id',
        'authenticatable_type',
        'deleted_at',
    ];

    protected $casts = [
        'last_activity_at' => 'timestamp',
        'authenticatable_id' => 'int',
    ];

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function touchLastActivity(): bool
    {
        return $this->touch(attribute: 'last_activity_at');
    }

    public function isCurrent(string $sessionId, string $guardName): bool
    {
        return $this->session_id == $sessionId && $this->guard_name = $guardName;
    }

    public function isMfaCompleted(): bool
    {
        return filled(value: $this->mfa_completed_at);
    }
}
