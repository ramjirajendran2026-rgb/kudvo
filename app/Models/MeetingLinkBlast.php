<?php

namespace App\Models;

use App\Enums\MeetingLinkBlastStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MeetingLinkBlast extends Model
{
    use LogsActivity;

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
        'meeting_id',
    ];

    protected $casts = [
        'scheduled_at' => 'immutable_datetime',
        'initiated_at' => 'immutable_datetime',
        'completed_at' => 'immutable_datetime',
        'cancelled_at' => 'immutable_datetime',
        'total_electors' => 'int',
        'processed_electors' => 'int',
        'is_reminder' => 'bool',
        'meeting_id' => 'int',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(related: Meeting::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('scheduled_at', '<=', now())
            ->whereNull('initiated_at')
            ->whereNull('job_batch_id');
    }

    public function scopeActiveMeeting(Builder $query): Builder
    {
        return $query->whereHas(
            relation: 'meeting',
            callback: function (Builder $query) {
                $query->scopes(scopes: ['published']);
            }
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes): MeetingLinkBlastStatus => match (true) {
                filled($this->cancelled_at) => MeetingLinkBlastStatus::Cancelled,
                filled($this->completed_at) => MeetingLinkBlastStatus::Completed,
                filled($this->initiated_at) => MeetingLinkBlastStatus::Running,
                $this->scheduled_at->isPast() => MeetingLinkBlastStatus::Pending,
                default => MeetingLinkBlastStatus::Scheduled,
            },
        );
    }
}
