<?php

namespace App\Models;

use App\Enums\OneTimePasswordPurpose;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes): bool => $this->expires_at->isPast() || $this->verified_at?->isPast(),
        );
    }

    public function relatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function routeNotificationForSms(mixed $notification = null): ?string
    {
        return $this->phone;
    }

    public function send(): void
    {
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
