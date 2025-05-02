<?php

namespace App\Models;

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

class Member extends Model implements AuthenticatableContract, AuthorizableContract
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
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'organisation_id' => 'int',
        'branch_id' => 'int',
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

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => collect([$this->title, $this->full_name])
                ->filter()->implode(' ') ?: 'Member',
        );
    }
}
