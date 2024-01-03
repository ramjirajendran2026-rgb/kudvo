<?php

namespace App\Models;

use App\Enums\NominationStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Nomination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'self_nomination',
        'nominator_threshold',
        'timezone',
        'starts_at',
        'ends_at',
        'withdrawal_starts_at',
        'withdrawal_ends_at',
        'published_at',
        'closed_at',
        'scrutinised_at',
        'cancelled_at',
        'organisation_id',
    ];

    protected $casts = [
        'self_nomination' => 'bool',
        'nominator_threshold' => 'int',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'withdrawal_starts_at' => 'datetime',
        'withdrawal_ends_at' => 'datetime',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
        'scrutinised_at' => 'datetime',
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
                filled(value: $this->cancelled_at) => NominationStatusEnum::CANCELLED,
                filled(value: $this->scrutinised_at) => NominationStatusEnum::SCRUTINISED,
                filled(value: $this->closed_at) => NominationStatusEnum::CLOSED,
                filled(value: $this->published_at) => NominationStatusEnum::PUBLISHED,
                default => NominationStatusEnum::DRAFT,
            },
        );
    }

    protected function isDraft(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->status === NominationStatusEnum::DRAFT,
        );
    }

    protected function isPublished(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->status === NominationStatusEnum::PUBLISHED,
        );
    }

    protected function isClosed(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->status === NominationStatusEnum::CLOSED,
        );
    }

    protected function isScrutinised(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->status === NominationStatusEnum::SCRUTINISED,
        );
    }

    protected function isCancelled(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->status === NominationStatusEnum::CANCELLED,
        );
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(related: Organisation::class);
    }

    public function preference(): HasOne
    {
        return $this->hasOne(related: NominationPreference::class)
            ->latestOfMany();
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
        return $this->morphMany(
            related: Position::class,
            name: 'event',
        );
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'cancelled_at');
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'closed_at')
            ->whereNull(columns: 'scrutinised_at')
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

    public function scopeScrutinised(Builder $query): Builder
    {
        return $query->whereNotNull('scrutinised_at')
            ->whereNull(columns: 'cancelled_at');
    }

    protected static function booted(): void
    {
        static::creating(callback: function (Nomination $nomination) {
            if (blank($nomination->code)) {
                $nomination->code = static::generateCode();
            }
        });
    }

    public function isTimingConfigured(): bool
    {
        return filled(value: $this->starts_at) &&
            filled(value: $this->ends_at) &&
            filled(value: $this->timezone);
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

    public static function generateCode(): string
    {
        return config(key: 'app.nomination.code.prefix').
            Str::upper(value: Str::random(length: config(key: 'app.nomination.code.length')));
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
