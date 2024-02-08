<?php

namespace App\Models;

use App\Data\ElectionPreferenceData;
use App\Data\WebAppManifestData;
use App\Enums\ElectionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Election extends Model
{
    protected $fillable = [
        'name',
        'description',
        'preference',
        'web_app_manifest',
        'timezone',
        'starts_at',
        'ends_at',
        'published_at',
        'closed_at',
        'completed_at',
        'cancelled_at',
        'organisation_id',
    ];

    protected $casts = [
        'preference' => ElectionPreferenceData::class,
        'web_app_manifest' => WebAppManifestData::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'organisation_id' => 'int',
    ];

    protected function startsAtLocal(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->starts_at?->tz(value: $this->timezone ?? 'UTC'),
        );
    }

    protected function endsAtLocal(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->ends_at?->tz(value: $this->timezone ?? 'UTC'),
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => match (true) {
                filled(value: $this->cancelled_at) => ElectionStatus::CANCELLED,
                filled(value: $this->completed_at) => ElectionStatus::COMPLETED,
                filled(value: $this->closed_at) => ElectionStatus::CLOSED,
                filled(value: $this->published_at) => ElectionStatus::PUBLISHED,
                default => ElectionStatus::DRAFT,
            },
        );
    }

    protected function isDraft(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->status === ElectionStatus::DRAFT,
        );
    }

    protected function isPublished(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->status === ElectionStatus::PUBLISHED,
        );
    }

    protected function isUpcoming(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->is_published && $this->starts_at->isFuture(),
        );
    }

    protected function isOpen(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->is_published && $this->starts_at->isPast() && $this->ends_at->isFuture(),
        );
    }

    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->is_published && $this->ends_at->isPast(),
        );
    }

    protected function isClosed(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->status === ElectionStatus::CLOSED,
        );
    }

    protected function isCompleted(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->status === ElectionStatus::COMPLETED,
        );
    }

    protected function isCancelled(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->status === ElectionStatus::CANCELLED,
        );
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(related: Organisation::class);
    }

    public function electors(): MorphMany
    {
        return $this->morphMany(
            related: Elector::class,
            name: 'event',
        );
    }

    public function positions(): MorphMany
    {
        return $this
            ->morphMany(
                related: Position::class,
                name: 'event',
            )
            ->oldest(column: 'sort');
    }

    public function monitorTokens(): HasMany
    {
        return $this->hasMany(related: ElectionMonitorToken::class);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'cancelled_at');
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'closed_at')
            ->whereNull(columns: 'completed_at')
            ->whereNull(columns: 'cancelled_at');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->whereNull(columns: 'published_at')
            ->whereNull(columns: 'cancelled_at');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'published_at')
            ->whereNull(columns: 'closed_at')
            ->whereNull(columns: 'cancelled_at');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'completed_at')
            ->whereNull(columns: 'closed_at')
            ->whereNull(columns: 'cancelled_at');
    }

    protected static function booted(): void
    {
        static::creating(callback: function (Election $election) {
            if (blank($election->code)) {
                $election->code = static::generateCode();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public static function generateCode(): string
    {
        return config(key: 'app.election.code.prefix').
            Str::upper(value: Str::random(length: config(key: 'app.election.code.length')));
    }

    public function isTimingConfigured(): bool
    {
        return filled(value: $this->starts_at) &&
            filled(value: $this->ends_at) &&
            filled(value: $this->timezone);
    }

    public function isMfaRequired(): bool
    {
        return $this->preference->mfa_sms || $this->preference->mfa_mail;
    }

    public function isMfaSmsAutoFillOnly(): bool
    {
        return $this->preference->mfa_sms_auto_fill_only;
    }

    public function isPwaEnabled(): bool
    {
        return filled($this->web_app_manifest);
    }

    public function getElectorGroups(): array
    {
        return $this
            ->electors()
            ->select(columns: ['groups'])
            ->whereNotNull(columns: 'groups')
            ->distinct()
            ->pluck(column: 'groups')
            ->map(callback: fn (string $item): array => explode(separator: ',', string: $item))
            ->flatten()
            ->unique()
            ->toArray();
    }

    public function cancel(): bool
    {
        return $this->touch(attribute: 'cancelled_at');
    }

    public function close(): bool
    {
        return $this->touch(attribute: 'closed_at');
    }

    public function publish(): bool
    {
        return $this->touch(attribute: 'published_at');
    }
}
