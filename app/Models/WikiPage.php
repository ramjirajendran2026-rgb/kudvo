<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Schema\ArticleSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class WikiPage extends Model implements HasMedia, Sitemapable
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

    protected function coverUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes): string => $this->getFirstMediaUrl(static::MEDIA_COLLECTION_COVER),
        );
    }

    public static function getDefaultCoverUrl(): string
    {
        return secure_asset('img/default-cover.webp');
    }

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(static::MEDIA_COLLECTION_COVER)
            ->singleFile()
            ->useFallbackUrl(secure_asset('img/default-cover.webp'))
            ->useFallbackPath(asset('img/default-cover.webp'));
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            image: $this->coverUrl,
            schema: SchemaCollection::make()
                ->addArticle(
                    fn (ArticleSchema $schema, SEOData $data): ArticleSchema => $schema
                        ->markup(
                            fn (Collection $markup): Collection => $markup
                                ->put('author', [
                                    '@type' => 'Organization',
                                    'name' => config('app.name'),
                                    'url' => config('app.url'),
                                ])
                        )
                )
                ->add(fn (SEOData $data): array => [
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'Home',
                            'item' => config('app.url'),
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => 'Wiki',
                            'item' => route('wiki.index'),
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $this->seo?->title ?? $this->title,
                        ],
                    ],
                ])
        );
    }

    public function toSitemapTag(): Url | string | array
    {
        return Url::create(route('wiki.show', $this))
            ->setLastModificationDate($this->updated_at)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.1);
    }
}
