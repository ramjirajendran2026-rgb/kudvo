<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CandidateGroup extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'short_name',
        'election_id',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(related: Election::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(related: Candidate::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
