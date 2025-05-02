<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Segment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'election_id',
    ];

    protected $casts = [
        'election_id' => 'int',
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

    public function electors(): BelongsToMany
    {
        return $this->belongsToMany(related: Elector::class)
            ->withTimestamps();
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(related: Position::class)
            ->withTimestamps();
    }
}
