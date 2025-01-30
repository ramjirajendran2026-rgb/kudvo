<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use MattDaneshvar\Survey\Models\Survey as BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Survey extends BaseModel implements HasMedia
{
    use InteractsWithMedia;

    const MEDIA_COLLECTION_FOOTER_IMAGES = 'footer_images';

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)
            ->orderBy('sort');
    }
}
