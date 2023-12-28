<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Election extends Model
{
    protected $fillable = [
        'name',
        'description',
        'timezone',
        'starts_at',
        'ends_at',
        'organisation_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'organisation_id' => 'int',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(related: Organisation::class);
    }

    protected static function booted(): void
    {
        static::creating(callback: function (Election $election) {
            if (blank($election->code)) {
                $election->code = 'EL'.Str::upper(value: Str::random(length: 8));
            }
        });
    }
}
