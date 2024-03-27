<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia, HasName, HasTenants, MustVerifyEmail
{
    use Billable;
    use HasApiTokens;
    use HasFactory;
    use InteractsWithMedia;
    use Notifiable;

    public const MEDIA_COLLECTION_AVATAR = 'avatar';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'phone_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(related: Organisation::class)
            ->using(class: OrganisationUser::class)
            ->withPivot(columns: ['role']);
    }

    protected static function booted(): void
    {
        static::updating(callback: function (User $user) {
            if ($user->isDirty(attributes: 'email') && $user->hasVerifiedEmail()) {
                $user->email_verified_at = null;
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'user' => true,
            'admin' => $this->email == 'iliyas.inode@gmail.com',
            default => false,
        };
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->organisations()->whereKey(id: $tenant->getKey())->exists();
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->organisations;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl(collectionName: static::MEDIA_COLLECTION_AVATAR);
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }
}
