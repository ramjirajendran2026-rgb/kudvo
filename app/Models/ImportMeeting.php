<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ImportMeeting extends Pivot
{
    use LogsActivity;

    protected $fillable = [
        'import_id',
        'meeting_id',
        'options',
        'column_map',
    ];

    protected $casts = [
        'import_id' => 'int',
        'meeting_id' => 'int',
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
