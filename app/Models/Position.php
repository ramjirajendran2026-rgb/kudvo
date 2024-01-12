<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Position extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'quota',
        'threshold',
        'elector_groups',
        'sort',
        'event_id',
        'event_type',
    ];

    protected $casts = [
        'quota' => 'int',
        'threshold' => 'int',
        'elector_groups' => 'array',
        'sort' => 'int',
        'event_id' => 'int',
    ];

    protected $appends = [
        'abstain',
    ];

    protected function abstain(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => filled(value: $this->threshold) && $this->threshold != $this->quota,
        );
    }

    public function event(): MorphTo
    {
        return $this->morphTo();
    }

    public function nominees(): HasMany
    {
        return $this->hasMany(related: Nominee::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(related: Candidate::class);
    }

    protected static function booted(): void
    {
        static::saving(callback: function (Position $position) {
            $position->threshold = blank($position->threshold) || $position->threshold > $position->quota ?
                $position->quota
                : $position->threshold;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}
