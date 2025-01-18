<?php

namespace App\Models;

use App\Actions\Meeting\GenerateMeetingShortKey;
use App\Enums\MeetingOnboardingStep;
use App\Enums\MeetingStatus;
use App\Enums\MeetingVotingStatus;
use App\Models\Concerns\HasNextPossibleKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Meeting extends Model
{
    use HasNextPossibleKey;
    use HasUlids;

    protected $fillable = [
        'name',
        'description',
        'timezone',
        'voting_starts_at',
        'voting_ends_at',
        'voting_closed_at',
        'published_at',
        'cancelled_at',
        'organisation_id',
    ];

    protected $casts = [
        'voting_starts_at' => 'datetime',
        'voting_ends_at' => 'datetime',
        'voting_closed_at' => 'datetime',
        'published_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'organisation_id' => 'int',
    ];

    protected function votingStartsAtLocal(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->voting_starts_at?->tz(value: $this->timezone),
        );
    }

    protected function votingEndsAtLocal(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->voting_ends_at?->tz(value: $this->timezone),
        );
    }

    protected function isCancelled(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => filled($this->cancelled_at),
        );
    }

    protected function isPublished(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => filled($this->published_at) && ! $this->is_cancelled,
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => match (true) {
                $this->is_cancelled => MeetingStatus::Cancelled,
                $this->is_published => MeetingStatus::Published,
                default => MeetingStatus::Onboarding,
            },
        );
    }

    protected function votingStatus(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => match (true) {
                filled($this->voting_closed_at) => MeetingVotingStatus::Closed,
                $this->voting_ends_at?->isPast() => MeetingVotingStatus::Ended,
                $this->voting_starts_at?->isPast() => MeetingVotingStatus::Open,
                $this->voting_starts_at?->isFuture() => MeetingVotingStatus::Scheduled,
                default => MeetingVotingStatus::NotApplicable,
            },
        );
    }

    public function meetingLinkBlasts(): HasMany
    {
        return $this->hasMany(MeetingLinkBlast::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull(columns: 'published_at');
    }

    protected static function booted(): void
    {
        static::creating(callback: function (self $meeting) {
            if (blank($meeting->code)) {
                $meeting->code = static::generateCode();
            }
        });

        static::saving(function (self $model) {
            if (blank($model->short_key)) {
                $model->short_key = app(GenerateMeetingShortKey::class)->execute();
            }
        });
    }

    public static function generateCode(): string
    {
        return config(key: 'app.meeting.code.prefix') .
            Str::upper(value: Str::random(length: config(key: 'app.meeting.code.length')));
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(related: Organisation::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(related: Participant::class);
    }

    public function votedParticipants(): HasMany
    {
        return $this->hasMany(related: Participant::class)
            ->scopes(scopes: ['voted']);
    }

    public function nonVotedParticipants(): HasMany
    {
        return $this->hasMany(related: Participant::class)
            ->scopes(scopes: ['nonVoted']);
    }

    public function resolutions(): HasMany
    {
        return $this->hasMany(related: Resolution::class)
            ->orderBy(column: 'sort');
    }

    public function participantEmails(): HasManyThrough
    {
        return $this->hasManyThrough(
            Email::class,
            Participant::class,
            'meeting_id',
            'notifiable_id',
        )->where('notifiable_type', Participant::class);
    }

    public function participantSmsMessages(): HasManyThrough
    {
        return $this->hasManyThrough(
            SmsMessage::class,
            Participant::class,
            'meeting_id',
            'smsable_id',
        )->where('smsable_type', Participant::class);
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /**
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['key'];
    }

    public function getOnboardingStep(): ?MeetingOnboardingStep
    {
        if (! $this->participants()->exists()) {
            return MeetingOnboardingStep::AddParticipants;
        }

        if (! $this->resolutions()->exists()) {
            return MeetingOnboardingStep::AddResolutions;
        }

        if (! $this->isStatus(MeetingStatus::Published)) {
            return MeetingOnboardingStep::Publish;
        }

        return null;
    }

    /**
     * @param  MeetingStatus|array<int, MeetingStatus>  $status
     */
    public function isStatus(MeetingStatus | array $status): bool
    {
        return in_array($this->status, Arr::wrap($status), true);
    }

    /**
     * @param  MeetingVotingStatus|array<int, MeetingVotingStatus>  $status
     */
    public function isVotingStatus(MeetingVotingStatus | array $status): bool
    {
        return in_array($this->voting_status, Arr::wrap($status), true);
    }
}
