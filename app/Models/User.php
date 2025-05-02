<?php

namespace App\Models;

use App\Enums\RolesEnum;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia, HasName, HasTenants, MustVerifyEmail
{
    use Billable;
    use CausesActivity;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
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
            'user' => $this->hasUserRole(),
            'admin' => $this->hasAdminRole() || $this->hasStaffRole(),
            default => false,
        };
    }

    public function hasUserRole(): bool
    {
        return $this->hasRole(RolesEnum::User);
    }

    public function hasAdminRole(): bool
    {
        return $this->hasRole(RolesEnum::Admin);
    }

    public function hasStaffRole(): bool
    {
        return $this->hasRole(RolesEnum::Staff);
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return match (true) {
            $tenant instanceof Organisation => $this->isBelongsToOrganisation($tenant)
                || $tenant->elections()->whereUser(user: $this)->exists(),
            default => false,
        };
    }

    public function isBelongsToOrganisation(Organisation $organisation): bool
    {
        if ($this->relationLoaded('organisations')) {
            return $this->organisations->contains($organisation);
        }

        return $this->organisations()->whereKey($organisation)->exists();
    }

    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(related: Organisation::class)
            ->using(class: OrganisationUser::class)
            ->withPivot(columns: ['role']);
    }

    public function elections(): BelongsToMany
    {
        return $this->belongsToMany(related: Election::class)
            ->as(accessor: 'collaboration')
            ->using(class: ElectionUser::class)
            ->withPivot(columns: ['designation', 'permissions'])
            ->withTimestamps();
    }

    public function getTenants(Panel $panel): array | Collection
    {
        $this->loadCollaboratedOrganisations();

        return $this->organisations
            ->merge(items: $this->collaboratedOrganisations)
            ->unique($this->getKeyName());
    }

    protected function loadCollaboratedOrganisations(): void
    {
        if (isset($this->relations['collaboratedOrganisations'])) {
            return;
        }

        $this->relations['collaboratedOrganisations'] = Organisation::query()
            ->whereHas(
                relation: 'elections',
                callback: fn (Builder $query) => $query->whereBelongsTo(related: Filament::auth()->user(), relationshipName: 'owner')
                    ->orWhereHas(
                        relation: 'collaborators',
                        callback: fn ($query) => $query->whereKey(Filament::auth()->id())
                    )
            )
            ->get();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl(collectionName: static::MEDIA_COLLECTION_AVATAR);
    }

    public function getFilamentName(): string
    {
        return $this->name ?: (Filament::getTenant()?->name ?: 'Admin');
    }

    public function canImpersonate(): bool
    {
        return $this->hasAdminRole();
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->name ?: $this->email,
        );
    }
}
