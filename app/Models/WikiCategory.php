<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class WikiCategory extends Model
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
}
