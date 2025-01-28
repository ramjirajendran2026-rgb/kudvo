<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use MattDaneshvar\Survey\Models\Survey as BaseModel;

class Survey extends BaseModel
{
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)
            ->orderBy('sort');
    }
}
