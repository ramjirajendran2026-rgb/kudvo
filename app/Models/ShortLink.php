<?php

namespace App\Models;

use App\Actions\GenerateShortLinkKey;
use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    protected $fillable = [
        'destination',
    ];

    protected static function booted(): void
    {
        static::saving(function (ShortLink $model) {
            if (blank($model->key)) {
                $model->key = app(GenerateShortLinkKey::class)->execute();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }
}
