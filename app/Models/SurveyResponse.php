<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class SurveyResponse extends Model implements Sortable
{
    use SortableTrait;

    protected $fillable = [
        'ip_address',
        'user_agent',
        'referrer_code',
        'sort',
        'survey_id',
        'user_id',
    ];

    protected $casts = [
        'sort' => 'int',
        'survey_id' => 'int',
        'user_id' => 'int',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'response_id');
    }

    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('survey_id', $this->survey_id);
    }
}
