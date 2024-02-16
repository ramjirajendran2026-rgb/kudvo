<?php

namespace App\Models;

use App\Data\Election\ResultMetaData;
use App\Data\EncryptedDataCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectionResult extends Model
{
    protected $fillable = [
        'total_votes',
        'processed_votes',
        'completed_at',
        'meta',
        'election_id',
    ];

    protected $casts = [
        'total_votes' => 'int',
        'processed_votes' => 'int',
        'completed_at' => 'datetime',
        'meta' => EncryptedDataCollection::class.':'.ResultMetaData::class.',default',
        'election_id' => 'int',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(related: Election::class);
    }
}
