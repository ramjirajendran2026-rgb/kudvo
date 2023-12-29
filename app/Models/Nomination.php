<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'cancelled_at' => 'datetime',
        'organisation_id' => 'int',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(related: Organisation::class);
    }

    public function electors(): MorphMany
    {
        return $this->morphMany(
            related: Elector::class,
            name: 'event',
        );
    }

    protected static function booted(): void
    {
        static::creating(callback: function (Nomination $nomination) {
            if (blank($nomination->code)) {
                $nomination->code = static::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        return config(key: 'app.nomination.code.prefix').
            Str::upper(value: Str::random(length: config(key: 'app.nomination.code.length')));
    }
}
