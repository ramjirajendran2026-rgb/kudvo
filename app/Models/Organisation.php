<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organisation extends Model
{
    protected $fillable = [
        'code',
        'name',
        'country',
        'timezone',
    ];

    protected static function booted(): void
    {
        static::creating(callback: function (Organisation $organisation) {
            if (blank($organisation->code)) {
                $organisation->code = static::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        return config(key: 'app.organisation.code.prefix').
            Str::upper(value: Str::random(length: config(key: 'app.organisation.code.length')));
    }
}
