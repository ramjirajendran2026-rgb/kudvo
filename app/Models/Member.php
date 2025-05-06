<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class Member extends Model implements AuthenticatableContract, AuthorizableContract, FilamentUser, HasName
{
    use Authenticatable;
    use Authorizable;
    use CausesActivity;
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'weightage',
        'membership_number',
        'membership_type',
        'membership_end_date',
        'is_active',
        'additional_data',
        'password',
        'remember_token',
        'email_verified_at',
        'phone_verified_at',
        'organisation_id',
        'branch_id',
    ];

    protected $casts = [
        'weightage' => 'double',
        'membership_end_date' => 'date',
        'is_active' => 'bool',
        'additional_data' => 'array',
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'organisation_id' => 'int',
        'branch_id' => 'int',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'display_name',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match (true) {
            $panel->getId() === 'member' => true,
            default => false,
        };
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => collect([$this->title, $this->full_name])
                ->filter()->implode(' ') ?: 'Member',
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => collect([$this->title, $this->full_name])
                ->filter()->implode(' ') ?: 'Member',
        );
    }
}
