<?php

namespace App\Models;

use App\Enums\CandidateSort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class Position extends Model implements Sortable
{
    use HasTranslations;
    use HasUuids;
    use LogsActivity;
    use SortableTrait;

    public array $translatable = [
        'name',
    ];

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

    protected static function booted(): void
    {
        static::saving(callback: function (Position $position) {
            $position->threshold = blank($position->threshold) || $position->threshold > $position->quota ?
                $position->quota
                : $position->threshold;
        });

        static::deleting(callback: function (Position $position) {
            $position->candidates()->cursor()->each->delete();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function event(): MorphTo
    {
        return $this->morphTo();
    }

    public function segments(): BelongsToMany
    {
        return $this->belongsToMany(related: Segment::class)
            ->withTimestamps();
    }

    public function nominees(): HasMany
    {
        return $this->hasMany(related: Nominee::class);
    }

    public function getOrderedCandidates(?CandidateSort $sort = null)
    {
        return $this->candidates
            ->when(
                $sort ?? $this->event?->preference?->candidate_sort,
                fn (Collection $collection, CandidateSort $sort) => match ($sort) {
                    CandidateSort::ASCENDING => $collection->sortBy('full_name'),
                    CandidateSort::DESCENDING => $collection->sortBy('full_name', descending: true),
                    CandidateSort::RANDOM => $collection->shuffle(),
                    default => $collection,
                }
            );
    }

    public function allCandidates(): HasMany
    {
        return $this->candidates()
            ->withoutGlobalScope(scope: 'disabled');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(related: Candidate::class)
            ->oldest(column: 'sort');
    }

    public function rankedCandidates(): HasMany
    {
        return $this->hasMany(related: Candidate::class)
            ->oldest(column: 'rank')
            ->oldest(column: 'sort');
    }

    public function candidateGroups(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: CandidateGroup::class,
            through: Candidate::class,
        );
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('event_id', $this->event_id);
    }

    public function getFallbackLocale()
    {
        return $this->locales()[0] ?? config('app.locale');
    }

    public function isUnopposed(): bool
    {
        return $this->candidates()->count() <= $this->quota;
    }

    protected function abstain(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => filled(value: $this->threshold) && $this->threshold != $this->quota,
        );
    }
}
