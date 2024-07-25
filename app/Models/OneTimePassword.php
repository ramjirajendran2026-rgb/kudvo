<?php

namespace App\Models;

use App\Enums\OneTimePasswordPurpose;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class OneTimePassword extends Model
{
    use HasUuids;
    use Notifiable;

    protected $fillable = [
        'code',
        'purpose',
        'email',
        'phone',
        'total_sent',
        'total_attempt',
        'expires_at',
        'sent_at',
        'verified_at',
        'relatable_id',
        'relatable_type',
    ];

    protected $casts = [
        'code' => 'encrypted',
        'purpose' => OneTimePasswordPurpose::class,
        'total_sent' => 'int',
        'total_attempt' => 'int',
        'expires_at' => 'datetime',
        'sent_at' => 'datetime',
        'verified_at' => 'datetime',
        'relatable_id' => 'int',
    ];

    public function relatable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::creating(callback: function (OneTimePassword $oneTimePassword) {
            $oneTimePassword->code ??= rand(min: 100000, max: 999999);
            $oneTimePassword->expires_at ??= now()->addMinutes(value: 15);
        });
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function routeNotificationForSms(mixed $notification = null): ?string
    {
        return $this->phone;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast() || $this->verified_at?->isPast();
    }

    public function isVerified(): bool
    {
        return (bool) $this->verified_at?->isPast();
    }

    public function send(?Notification $notification = null): void
    {
        if (filled(value: $notification)) {
            $this->notify(instance: $notification);
        }

        $this->increment(
            column: 'total_sent',
            extra: ['sent_at' => $this->freshTimestamp()]
        );
    }

    public function verify(string $code): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        if ($this->code != $code) {
            return false;
        }

        $this->verified_at = $this->freshTimestamp();

        return $this->save();
    }
}
