<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected $appends = [
        'content_formatted',
    ];

    protected function contentFormatted(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => is_array($this->content) ? implode(', ', $this->content) : $this->content,
        );
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class);
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class);
    }
}
