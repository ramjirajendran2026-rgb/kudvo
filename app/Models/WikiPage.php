<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WikiPage extends Model implements HasMedia
{
    use HasFactory;
    use HasSEO;
    use InteractsWithMedia;
    use SoftDeletes;

    public const MEDIA_COLLECTION_COVER = 'cover';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'published_at',
        'category_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'category_id' => 'int',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(WikiCategory::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(WikiTag::class);
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        $routeKeyName = $field ?? $this->getRouteKeyName();

        if ($routeKeyName == 'slug') {
            $value = last(explode(separator: '-', string: $value));
            $routeKeyName = $this->getKeyName();
        }

        return $query->where($routeKeyName, $value);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::created(callback: function (WikiPage $wikiPage) {
            $wikiPage->slug = Str::slug(title: $wikiPage->title . ' ' . $wikiPage->getKey());
            $wikiPage->saveQuietly();
        });

        static::updating(callback: function (WikiPage $wikiPage) {
            if ($wikiPage->isDirty(attributes: 'title')) {
                $wikiPage->slug = Str::slug(title: $wikiPage->title . ' ' . $wikiPage->getKey());
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
