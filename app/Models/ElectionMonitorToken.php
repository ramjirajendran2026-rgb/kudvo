<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use Jenssegers\Agent\Agent;

class ElectionMonitorToken extends Model
{
    use HasUuids;
    use SoftDeletes;

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

    protected function userAgent(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => filled($value) ? new Agent(userAgent: $value) : null,
            set: fn ($value) => $value,
        );
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(related: Election::class);
    }

    public function scopeActivated(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'activated_at');
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    public function uniqueIds(): array
    {
        return ['key'];
    }

    public function isActivated(): bool
    {
        return filled($this->activated_at);
    }

    public function getLink(): string
    {
        return URL::signedRoute(name: 'filament.election.pages.monitor', parameters: ['election' => $this->election, 'token' => $this->getRouteKey()]);
    }
}
