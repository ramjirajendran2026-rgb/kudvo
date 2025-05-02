<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SolutionForest\FilamentTree\Concern\ModelTree;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Branch extends Model
{
    use LogsActivity;
    use ModelTree;

    protected $fillable = [
        'code',
        'name',
        'order',
        'parent_id',
        'organisation_id',
    ];

    protected $casts = [
        'order' => 'int',
        'parent_id' => 'int',
        'organisation_id' => 'int',
    ];

    protected $appends = [
        'display_name',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'parent_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => "$this->name ($this->code)",
        );
    }
}
