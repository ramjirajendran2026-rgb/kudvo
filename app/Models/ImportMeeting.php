<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ImportMeeting extends Pivot
{
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
}
