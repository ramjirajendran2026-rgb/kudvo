<?php

namespace App\Models;

use App\Enums\SurveyQuestionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class SurveyQuestion extends Model implements Sortable
{
    use SortableTrait;

    public const KEY_PREFIX = 'SQ';

    protected $fillable = [
        'text',
        'type',
        'options',
        'has_other_option',
        'is_required',
        'settings',
        'sort',
        'survey_id',
    ];

    protected $casts = [
        'type' => SurveyQuestionType::class,
        'options' => 'array',
        'has_other_option' => 'boolean',
        'is_required' => 'boolean',
        'settings' => 'array',
        'sort' => 'int',
        'survey_id' => 'int',
    ];

    protected function key(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => static::KEY_PREFIX . $this->getKey(),
        );
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'question_id');
    }

    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('survey_id', $this->survey_id);
    }
}
