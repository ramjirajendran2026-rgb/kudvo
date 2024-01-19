<?php

namespace App\Models;

use App\Facades\Kudvo;
use App\Filament\ElectionPanel;
use App\Filament\NominationPanel;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Agent;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Elector extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    FilamentUser,
    HasAvatar,
    HasMedia,
    HasName
{
    use Authenticatable;
    use Authorizable;
    use HasFactory;
    use HasUuids;
    use InteractsWithMedia;
    use Notifiable;

    public const MEDIA_COLLECTION_AVATAR = 'avatar';

    protected $fillable = [
        'membership_number',
        'title',
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'groups',
        'current_session_id',
        'event_id',
        'event_type',
    ];

    protected $casts = [
        'event_id' => 'int',
    ];

    protected $appends = [
        'display_name',
    ];

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->membership_number.
                (filled(value: $this->full_name) ? ' ('.$this->full_name.')' : ''),
        );
    }

    public function event(): MorphTo
    {
        return $this->morphTo();
    }

    public function oneTimePasswords(): MorphMany
    {
        return $this->morphMany(
            related: OneTimePassword::class,
            name: 'relatable',
        );
    }

    public function authSessions(): MorphMany
    {
        return $this->morphMany(
            related: AuthSession::class,
            name: 'authenticatable',
        );
    }

    public function authSession(): MorphOne
    {
        return $this
            ->morphOne(
                related: AuthSession::class,
                name: 'authenticatable',
            )
            ->latestOfMany();
    }

    protected static function booted(): void
    {
        static::saving(callback: function (Elector $elector) {
            if (!is_null($elector->groups)) {
                $elector->groups = collect(value: explode(separator: ',', string: $elector->groups))
                    ->map(callback: fn (string $item): string => trim(string: $item))
                    ->unique()
                    ->sort()
                    ->implode(value: ',') ?: null;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match (true) {
            $panel instanceof NominationPanel => $this->event->is(model: Kudvo::getNomination()),
            $panel instanceof ElectionPanel => $this->event->is(model: Kudvo::getElection()),
            default => false,
        };
    }

    public function getFilamentName(): string
    {
        return $this->full_name ?: $this->membership_number;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl(collectionName: static::MEDIA_COLLECTION_AVATAR);
    }

    public function routeNotificationForSms(?Notification $notification = null)
    {
        return $this->phone;
    }

    public function createAuthSession(string $sessionId, string $guardName, Request $request): AuthSession
    {
        $this->authSessions()
            ->where('guard_name', $guardName)
            ->delete();

        return $this->authSessions()
            ->create(attributes: [
                'session_id' => $sessionId,
                'guard_name' => $guardName,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
    }
}
