<?php

namespace App\Models;

use App\Data\Survey\SettingsData;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Survey extends Model implements HasMedia
{
    use HasUlids;
    use InteractsWithMedia;

    public const MEDIA_COLLECTION_HEADER = 'header';

    protected $fillable = [
        'title',
        'description',
        'settings',
        'is_active',
        'published_at',
        'organisation_id',
    ];

    protected $casts = [
        'settings' => SettingsData::class,
        'is_active' => 'boolean',
        'published_at' => 'immutable_datetime',
        'organisation_id' => 'int',
    ];

    protected $attributes = [
        'title' => 'Untitled Survey',
        'is_active' => true,
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)
            ->orderBy('sort');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class)
            ->orderBy('sort');
    }

    public function uniqueIds(): array
    {
        return ['ulid'];
    }
}
