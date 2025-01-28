<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use MattDaneshvar\Survey\Models\Question as BaseModel;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Question extends BaseModel implements Sortable
{
    use SortableTrait;

    protected $fillable = ['type', 'options', 'content', 'rules', 'sort', 'survey_id'];

    protected $casts = [
        'rules' => 'array',
        'options' => 'array',
        'sort' => 'int',
    ];

    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('survey_id', $this->survey_id);
    }
}
