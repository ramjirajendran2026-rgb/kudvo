<?php

namespace App\Models;

use App\Actions\GenerateParticipantShortKey;
use App\Models\Concerns\HasNextPossibleKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasNextPossibleKey;
    use HasUlids;

    protected $fillable = [
        'membership_number',
        'name',
        'email',
        'phone',
        'weightage',
        'voted_at',
        'meeting_id',
    ];

    protected $casts = [
        'weightage' => 'double',
        'voted_at' => 'datetime',
        'meeting_id' => 'int',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if (blank($model->short_key)) {
                $model->short_key = app(GenerateParticipantShortKey::class)->execute();
            }
        });
    }

    protected function isVoted(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => filled($attributes['voted_at']),
        );
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(related: Meeting::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(related: ResolutionVote::class);
    }

    public function scopeVoted(Builder $query): Builder
    {
        return $query->whereNotNull('voted_at');
    }

    /**
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['key'];
    }
}
