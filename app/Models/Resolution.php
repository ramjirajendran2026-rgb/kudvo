<?php

namespace App\Models;

use App\Enums\ResolutionChoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Resolution extends Model implements Sortable
{
    use SortableTrait;

    protected $fillable = [
        'name',
        'description',
        'allow_abstain_votes',
        'for_label',
        'against_label',
        'abstain_label',
        'sort',
        'meeting_id',
    ];

    protected $casts = [
        'allow_abstain_votes' => 'bool',
        'sort' => 'int',
        'meeting_id' => 'int',
    ];

    protected function forLabel(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => blank($value) ? ResolutionChoice::For->getLabel() : $value,
            set: fn ($value) => $value,
        );
    }

    protected function againstLabel(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => blank($value) ? ResolutionChoice::Against->getLabel() : $value,
        );
    }

    protected function abstainLabel(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => blank($value) ? ResolutionChoice::Abstain->getLabel() : $value,
            set: fn ($value) => $value,
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

    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('meeting_id', $this->meeting_id);
    }
}
