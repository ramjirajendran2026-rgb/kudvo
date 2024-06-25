<?php

namespace App\Models;

use App\Data\Election\VoteSecretData;
use App\Data\EncryptedDataCollection;
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
        'booth_id',
    ];

    protected $casts = [
        'secret' => EncryptedDataCollection::class.':'.VoteSecretData::class,
        'mock' => 'bool',
        'ballot_id' => 'int',
        'booth_id' => 'int',
    ];

    public $timestamps = false;

    public function ballot(): BelongsTo
    {
        return $this->belongsTo(related: Ballot::class);
    }

    public function booth(): BelongsTo
    {
        return $this->belongsTo(related: ElectionBoothToken::class);
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
