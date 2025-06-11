<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class CandidateFallbackPosition extends Model implements Sortable
{
    use LogsActivity;
    use SortableTrait;

    protected $fillable = [
        'sort',
        'candidate_id',
        'position_id',
    ];

    protected $casts = [
        'sort' => 'integer',
        'candidate_id' => 'integer',
        'position_id' => 'integer',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('candidate_id', $this->candidate_id);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
