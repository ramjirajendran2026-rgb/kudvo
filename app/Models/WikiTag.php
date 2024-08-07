<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class WikiTag extends Model
{
    use HasSEO;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'summary',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(WikiPage::class);
    }
}
