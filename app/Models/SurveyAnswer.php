<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyAnswer extends Model
{
    protected $fillable = [
        'content',
        'question_id',
        'response_id',
    ];

    protected $casts = [
        'content' => 'array',
        'question_id' => 'int',
        'response_id' => 'int',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class);
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class);
    }
}
