<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Vote extends Model
{
    use HasUuids;

    protected $fillable = [
        'key',
        'secret',
        'mock',
        'ballot_id',
    ];

    protected $casts = [
        'secret' => 'encrypted:array',
        'mock' => 'bool',
        'ballot_id' => 'int',
    ];

    public $timestamps = false;

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(related: Ballot::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(related: Position::class, foreignKey: 'key', ownerKey: 'uuid');
    }

    public function newUniqueId(): string
    {
        return (string) Str::uuid();
    }
}
