<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class WikiCategory extends Model implements Sitemapable
{
    use HasFactory;
    use HasSEO;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'summary',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(WikiPage::class, 'category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function hasCustomSlug(): bool
    {
        return $this->slug !== Str::slug($this->name);
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: 'Wiki: In-Depth Guides on ' . $this->name,
            description: 'Explore the Kudvo Wiki for expert insights on ' . $this->name . ', covering the latest trends, technologies, and strategies.',
            schema: SchemaCollection::make()
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
                            'name' => $this->name,
                        ],
                    ],
                ])
        );
    }

    public function toSitemapTag(): Url | string | array
    {
        return Url::create(route('wiki.categories.show', $this))
            ->setLastModificationDate($this->updated_at)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.1);
    }
}
