<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Elector extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'membership_number',
        'title',
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'groups',
        'event_id',
        'event_type',
    ];

    protected $casts = [
        'groups' => 'array',
        'event_id' => 'int',
    ];

    public function event(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::saving(callback: function (Elector $elector) {
            if (filled($elector->groups)) {
                $elector->groups = collect(value: $elector->groups)
                    ->map(callback: fn (string $item): string => trim(string: $item))
                    ->unique()
                    ->toArray();
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
