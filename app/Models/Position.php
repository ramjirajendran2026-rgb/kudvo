<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
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
        'elector_groups' => 'array',
        'sort' => 'int',
        'event_id' => 'int',
    ];

    public function event(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::saving(callback: function (Position $position) {
            if (blank($position->threshold) || $position->threshold > $position->quota) {
                $position->threshold = $position->quota;
            }
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
