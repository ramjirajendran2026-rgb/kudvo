<?php

namespace App\Models;

use App\Enums\EmailStatus;
use App\Enums\MailMessagePurpose;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Email extends Model
{
    protected $fillable = [
        'message_id',
        'subject',
        'to_address',
        'to_name',
        'from_address',
        'from_name',
        'bounced_at',
        'bounce_data',
        'complained_at',
        'complaint_data',
        'delivered_at',
        'delivery_delayed_at',
        'delivery_delay_data',
        'rejected_at',
        'reject_data',
        'rendering_failed_at',
        'rendering_failure_data',
        'sent_at',
        'subscription_notified_at',
        'subscription_data',
        'purpose',
        'notifiable_id',
        'notifiable_type',
    ];

    protected $casts = [
        'bounced_at' => 'immutable_datetime',
        'bounce_data' => 'array',
        'complained_at' => 'immutable_datetime',
        'complaint_data' => 'array',
        'delivered_at' => 'immutable_datetime',
        'delivery_delayed_at' => 'immutable_datetime',
        'delivery_delay_data' => 'array',
        'rejected_at' => 'immutable_datetime',
        'reject_data' => 'array',
        'rendering_failed_at' => 'immutable_datetime',
        'rendering_failure_data' => 'array',
        'sent_at' => 'immutable_datetime',
        'subscription_notified_at' => 'immutable_datetime',
        'subscription_data' => 'array',
        'purpose' => MailMessagePurpose::class,
        'notifiable_id' => 'int',
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => match (true) {
                $this->isComplained() => EmailStatus::Complaint,
                $this->isDelivered() => EmailStatus::Delivered,
                $this->isBounced() => EmailStatus::Bounced,
                $this->isRejected() => EmailStatus::Rejected,
                $this->isRenderingFailed() => EmailStatus::RenderingFailed,
                $this->isDeliveryDelayed() => EmailStatus::DeliveryDelayed,
                $this->isSent() => EmailStatus::Sent,
                $this->isPending() => EmailStatus::Pending,
                default => EmailStatus::Unknown,
            },
        );
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function opens(): HasMany
    {
        return $this->hasMany(related: EmailOpen::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(related: EmailClick::class);
    }

    public function scopeBounced(Builder $query): Builder
    {
        return $query->whereNotNull('bounced_at');
    }

    public function scopeComplained(Builder $query): Builder
    {
        return $query->whereNotNull('complained_at');
    }

    public function scopeDelivered(Builder $query): Builder
    {
        return $query->whereNotNull('delivered_at');
    }

    public function scopeDeliveryDelayed(Builder $query): Builder
    {
        return $query->whereNotNull('delivery_delayed_at');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->whereNotNull('rejected_at');
    }

    public function scopeRenderingFailed(Builder $query): Builder
    {
        return $query->whereNotNull('rendering_failed_at');
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->whereNotNull('sent_at');
    }

    public function scopeSubscriptionNotified(Builder $query): Builder
    {
        return $query->whereNotNull('subscription_notified_at');
    }

    public function scopeBallotLink(Builder $query): Builder
    {
        return $query->where('purpose', MailMessagePurpose::BallotLink);
    }

    public function scopeBallotMfaCode(Builder $query): Builder
    {
        return $query->where('purpose', MailMessagePurpose::BallotMfaCode);
    }

    public function scopeVotedConfirmation(Builder $query): Builder
    {
        return $query->where('purpose', MailMessagePurpose::VotedConfirmation);
    }

    public function scopeVotedBallotCopy(Builder $query): Builder
    {
        return $query->where('purpose', MailMessagePurpose::VotedBallotCopy);
    }

    public function isPending(): bool
    {
        return blank($this->sent_at);
    }

    public function isSent(): bool
    {
        return filled($this->sent_at);
    }

    public function isDelivered(): bool
    {
        return filled($this->delivered_at);
    }

    public function isBounced(): bool
    {
        return filled($this->bounced_at);
    }

    public function isRejected(): bool
    {
        return filled($this->rejected_at);
    }

    public function isComplained(): bool
    {
        return filled($this->complained_at);
    }

    public function isDeliveryDelayed(): bool
    {
        return filled($this->delivery_delayed_at);
    }

    public function isRenderingFailed(): bool
    {
        return filled($this->rendering_failed_at);
    }
}
