<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
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

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => collect([$this->title, $this->full_name])
                ->filter()->implode(' ') ?: 'Member',
        );
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
