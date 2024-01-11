<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Organisation extends Model implements HasAvatar, HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public const MEDIA_COLLECTION_LOGO = 'logo';

    protected $fillable = [
        'code',
        'name',
        'country',
        'timezone',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(related: User::class)
            ->using(class: OrganisationUser::class)
            ->withPivot(columns: ['role']);
    }

    public function nominations(): HasMany
    {
        return $this->hasMany(related: Nomination::class);
    }

    public function elections(): HasMany
    {
        return $this->hasMany(related: Election::class);
    }

    protected static function booted(): void
    {
        static::creating(callback: function (Organisation $organisation) {
            if (blank($organisation->code)) {
                $organisation->code = static::generateCode();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public static function generateCode(): string
    {
        return config(key: 'app.organisation.code.prefix').
            Str::upper(value: Str::random(length: config(key: 'app.organisation.code.length')));
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl(collectionName: static::MEDIA_COLLECTION_LOGO);
    }
}
