<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Organisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'country',
        'timezone',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(related: User::class)
            ->using(class: OrganisationUser::class)
            ->withPivot(columns: ['role']);
    }

    public function nominations(): HasMany
    {
        return $this->hasMany(related: Nomination::class);
    }

    protected static function booted(): void
    {
        static::creating(callback: function (Organisation $organisation) {
            if (blank($organisation->code)) {
                $organisation->code = static::generateCode();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public static function generateCode(): string
    {
        return config(key: 'app.organisation.code.prefix').
            Str::upper(value: Str::random(length: config(key: 'app.organisation.code.length')));
    }
}
