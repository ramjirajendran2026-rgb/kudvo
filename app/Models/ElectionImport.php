<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ElectionImport extends Pivot
{
    use LogsActivity;

    protected $fillable = [
        'election_id',
        'import_id',
        'options',
        'column_map',
    ];

    protected $casts = [
        'election_id' => 'int',
        'import_id' => 'int',
        'options' => 'array',
        'column_map' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
