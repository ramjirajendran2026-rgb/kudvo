<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ElectionImport extends Pivot
{
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
}
