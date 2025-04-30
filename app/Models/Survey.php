<?php

namespace App\Models;

use App\Data\Survey\SettingsData;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Survey extends Model implements HasMedia
{
    use HasSEO;
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
        'branch_id',
    ];

    protected $casts = [
        'settings' => SettingsData::class,
        'is_active' => 'boolean',
        'published_at' => 'immutable_datetime',
        'organisation_id' => 'int',
        'branch_id' => 'int',
    ];

    protected $attributes = [
        'title' => 'Untitled Survey',
        'is_active' => true,
    ];

    protected $appends = [
        'has_description',
    ];

    protected function hasDescription(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => filled($this->description),
        );
    }

    protected function isPublished(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => (bool) $this->published_at?->isPast(),
        );
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->title,
            description: str($this->description)->stripTags()->toString(),
        );
    }

    public function isPreviewable(): bool
    {
        return $this->questions()->exists();
    }
}
