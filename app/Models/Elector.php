<?php

namespace App\Models;

use App\Facades\Kudvo;
use App\Filament\Election\ElectionPanel;
use App\Filament\Nomination\NominationPanel;
use App\Models\Concerns\HasShortCode;
use App\Notifications\Election\BallotLinkNotification;
use App\Notifications\Election\VotingInstructionNotification;
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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Elector extends Model implements AuthenticatableContract, AuthorizableContract, FilamentUser, HasAvatar, HasMedia, HasName
{
    use Authenticatable;
    use Authorizable;
    use HasFactory;
    use HasShortCode;
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
            get: fn ($value, array $attributes) => collect(value: [$this->title, $this->full_name])
                ->filter(callback: fn (?string $item): bool => filled($item))
                ->implode(value: ' ')
        );
    }

    public function event(): MorphTo
    {
        return $this->morphTo();
    }

    public function segments(): BelongsToMany
    {
        return $this->belongsToMany(related: Segment::class)
            ->withTimestamps();
    }

    public function oneTimePasswords(): MorphMany
    {
        return $this->morphMany(
            related: OneTimePassword::class,
            name: 'relatable',
        );
    }

    public function booth(): HasOne
    {
        return $this->hasOne(related: ElectionBoothToken::class, foreignKey: 'current_elector_id')
            ->latestOfMany();
    }

    public function ballots(): HasMany
    {
        return $this->hasMany(related: Ballot::class)
            ->where('mock', false)
            ->latest();
    }

    public function ballot(): HasOne
    {
        return $this->hasOne(related: Ballot::class)
            ->where('mock', false)
            ->latestOfMany();
    }

    public function mockBallots(): HasMany
    {
        return $this->hasMany(related: Ballot::class)
            ->where('mock', true)
            ->latest();
    }

    public function mockBallot(): HasOne
    {
        return $this->hasOne(related: Ballot::class)
            ->where('mock', true)
            ->latestOfMany();
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

    public function emails(): MorphMany
    {
        return $this
            ->morphMany(
                related: Email::class,
                name: 'notifiable',
            );
    }

    public function ballotLinkEmails(): MorphMany
    {
        return $this->emails()
            ->scopes(scopes: ['ballotLink']);
    }

    public function mfaCodeEmails(): MorphMany
    {
        return $this->emails()
            ->scopes(scopes: ['ballotMfaCode']);
    }

    public function votedConfirmationEmails(): MorphMany
    {
        return $this->emails()
            ->scopes(scopes: ['votedConfirmation']);
    }

    public function votedBallotCopyEmails(): MorphMany
    {
        return $this->emails()
            ->scopes(scopes: ['votedBallotCopy']);
    }

    public function smsMessages(): MorphMany
    {
        return $this
            ->morphMany(
                related: SmsMessage::class,
                name: 'smsable',
            );
    }

    public function ballotLinkSmsMessages(): MorphMany
    {
        return $this->smsMessages()
            ->scopes(scopes: ['ballotLink']);
    }

    public function mfaCodeSmsMessages(): MorphMany
    {
        return $this->smsMessages()
            ->scopes(scopes: ['ballotMfaCode']);
    }

    public function votedConfirmationSmsMessages(): MorphMany
    {
        return $this->smsMessages()
            ->scopes(scopes: ['votedConfirmation']);
    }

    protected static function booted(): void
    {
        static::saving(callback: function (Elector $elector) {
            if (! is_null($elector->groups)) {
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

    public function routeNotificationForMail(?Notification $notification = null)
    {
        $validator = Validator::make(
            data: [
                'email' => $this->email,
            ],
            rules: [
                'email' => 'required|email:rfc,dns',
            ],
        );

        return $validator->passes() ? $this->email : null;
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

    public function notifyVotingInstructions(?Election $election = null, bool $now = false): void
    {
        $notification = new VotingInstructionNotification(
            election: $election ?? $this->event,
        );

        if ($now) {
            $this->notifyNow(instance: $notification);
        } else {
            $this->notify(instance: $notification);
        }
    }

    public function sendBallotLink(?Election $election = null, bool $now = false): void
    {
        /** @var Election $election */
        $election ??= $this->event;

        $notification = new BallotLinkNotification(
            elector: $this,
            election: $election,
        );

        if ($now) {
            $this->notifyNow(instance: $notification);
        } else {
            $this->notify(instance: $notification);
        }
    }
}
