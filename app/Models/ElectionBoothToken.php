<?php

namespace App\Models;

use App\Enums\ElectionBoothTokenStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ElectionBoothToken extends Model implements Sortable
{
    use HasUuids;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;

    protected $fillable = [
        'name',
        'group',
        'sort',
        'key',
        'activated_at',
        'ip_address',
        'user_agent',
        'election_id',
        'current_elector_id',
    ];

    protected $hidden = [
        'user_agent',
    ];

    protected $casts = [
        'sort' => 'int',
        'activated_at' => 'immutable_datetime',
        'election_id' => 'int',
        'current_elector_id' => 'int',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(related: Election::class);
    }

    public function currentElector(): BelongsTo
    {
        return $this->belongsTo(related: Elector::class);
    }

    public function ballots(): HasMany
    {
        return $this->hasMany(related: Ballot::class, foreignKey: 'booth_id')
            ->where('mock', false);
    }

    public function mockBallots(): HasMany
    {
        return $this->hasMany(related: Ballot::class, foreignKey: 'booth_id')
            ->where('mock', true);
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

    public function getLink(): string
    {
        return URL::signedRoute(name: 'filament.election.booth.activate', parameters: ['election' => $this->election, 'token' => $this->getRouteKey()]);
    }

    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('election_id', $this->election_id);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $value ?? $attributes['sort'],
            set: fn ($value) => $value,
        );
    }

    protected function userAgent(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => filled($value) ? new Agent(userAgent: $value) : null,
            set: fn ($value) => $value,
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes): ElectionBoothTokenStatus => match (true) {
                ! $this->isActivated() => ElectionBoothTokenStatus::PendingActivation,
                filled($this->current_elector_id) => ElectionBoothTokenStatus::Occupied,
                default => ElectionBoothTokenStatus::Available,
            },
        );
    }

    public function isActivated(): bool
    {
        return filled($this->activated_at);
    }
}
