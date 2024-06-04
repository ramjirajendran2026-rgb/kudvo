<?php

namespace App\Models;

use App\Enums\BallotLinkBlastStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BallotLinkBlast extends Model
{
    protected $fillable = [
        'scheduled_at',
        'initiated_at',
        'completed_at',
        'cancelled_at',
        'total_electors',
        'processed_electors',
        'is_reminder',
        'job_batch_id',
        'election_id',
        'total_electors',
        'processed_electors',
        'is_reminder',
        'job_batch_id',
        'election_id',
    ];

    protected $casts = [
        'scheduled_at' => 'immutable_datetime',
        'initiated_at' => 'immutable_datetime',
        'completed_at' => 'immutable_datetime',
        'cancelled_at' => 'immutable_datetime',
        'total_electors' => 'int',
        'processed_electors' => 'int',
        'is_reminder' => 'bool',
        'election_id' => 'int',
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes): BallotLinkBlastStatus => match (true) {
                filled($this->cancelled_at) => BallotLinkBlastStatus::Cancelled,
                filled($this->completed_at) => BallotLinkBlastStatus::Completed,
                filled($this->initiated_at) => BallotLinkBlastStatus::Running,
                $this->scheduled_at->isPast() => BallotLinkBlastStatus::Pending,
                default => BallotLinkBlastStatus::Scheduled,
            },
        );
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(related: Election::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereDate('scheduled_at', '>=', now())
            ->whereNull('initiated_at')
            ->whereNull('job_batch_id');
    }

    public function scopeActiveElection(Builder $query): Builder
    {
        return $query->whereHas(
            relation: 'election',
            callback: function (Builder $query) {
                $query->scopes(scopes: ['published']);
            }
        );
    }
}
