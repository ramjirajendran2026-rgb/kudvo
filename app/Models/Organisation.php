<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Color\Rgb;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Organisation extends Model implements HasAvatar, HasMedia
{
    use HasFactory;
    use HasTranslations;
    use InteractsWithMedia;

    public const MEDIA_COLLECTION_LOGO = 'logo';

    protected $fillable = [
        'code',
        'name',
        'country',
        'timezone',
    ];

    public array $translatable = ['name'];

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->getFirstMediaUrl(collectionName: static::MEDIA_COLLECTION_LOGO) ?:
                'https://ui-avatars.com/api/?name='.
                $this->name.
                '&color=FFFFFF&background='.
                str(Rgb::fromString('rgb('.FilamentColor::getColors()['primary'][800].')')->toHex())
                    ->after('#'),
        );
    }

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
