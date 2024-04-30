<?php

namespace App\Models;

use App\Data\Election\CollaboratorPermissionsData;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ElectionUser extends Pivot
{
    protected $casts = [
        'permissions' => CollaboratorPermissionsData::class,
        'election_id' => 'int',
        'user_id' => 'int',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(related: Election::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }
}
