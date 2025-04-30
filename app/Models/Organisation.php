<?php

namespace App\Models;

use Filament\Facades\Filament;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
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
            get: fn ($value, array $attributes) => Filament::getTenantAvatarUrl($this),
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(related: User::class)
            ->using(class: OrganisationUser::class)
            ->withPivot(columns: ['role']);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function nominations(): HasMany
    {
        return $this->hasMany(related: Nomination::class);
    }

    public function elections(): HasMany
    {
        return $this->hasMany(related: Election::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(related: Meeting::class);
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }

    protected static function booted(): void
    {
        static::creating(callback: function (Organisation $organisation) {
            if (blank($organisation->code)) {
                $organisation->code = static::generateCode();
            }
        });
    }

    public function scopeSearch(Builder $query, string $value, ?string $locale = null): Builder
    {
        $locale ??= app()->currentLocale();

        return $query->where(function (Builder $query) use ($value, $locale) {
            $query->where('code', $value)
                ->orWhereRaw(
                    'lower(json_unquote(json_extract(`name`, \'$."' . $locale . '"\'))) like lower(?)',
                    ['%' . $value . '%'],
                );
        });
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public static function generateCode(): string
    {
        return config(key: 'app.organisation.code.prefix') .
            Str::upper(value: Str::random(length: config(key: 'app.organisation.code.length')));
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl(collectionName: static::MEDIA_COLLECTION_LOGO);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(name: static::MEDIA_COLLECTION_LOGO)
            ->singleFile()
            ->withResponsiveImages();
    }
}
