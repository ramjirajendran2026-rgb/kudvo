<?php

namespace App\Models;

use App\Actions\GenerateShortLinkKey;
use App\Models\Concerns\HasNextPossibleKey;
use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    use HasNextPossibleKey;

    protected $fillable = [
        'destination',
    ];

    protected $casts = [
        'destination' => 'encrypted',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $model) {
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
