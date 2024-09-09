<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use Spatie\Color\Rgb;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Candidate extends Model implements HasAvatar, HasMedia, HasName, Sortable
{
    use HasFactory;
    use HasTranslations;
    use HasUuids;
    use InteractsWithMedia;
    use SortableTrait;

    public const MEDIA_COLLECTION_ATTACHMENTS = 'attachments';

    public const MEDIA_COLLECTION_BIO = 'bio';

    public const MEDIA_COLLECTION_PHOTO = 'photo';

    public const MEDIA_COLLECTION_SYMBOL = 'symbol';

    protected $fillable = [
        'membership_number',
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'disabled',
        'sort',
        'rank',
        'position_id',
        'candidate_group_id',
        'elector_id',
    ];

    protected $casts = [
        'disabled' => 'bool',
        'sort' => 'int',
        'rank' => 'int',
        'position_id' => 'int',
        'candidate_group_id' => 'int',
        'elector_id' => 'int',
    ];

    protected $appends = [
        'full_name',
        'display_name',
    ];

    protected array $translatable = [
        'title',
        'first_name',
        'last_name',
    ];

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => collect(value: [$this->first_name, $this->last_name])
                ->filter(callback: fn (?string $item): bool => filled($item))
                ->implode(value: ' '),
        );
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => collect(value: [$this->title, $this->full_name])
                ->filter(callback: fn (?string $item): bool => filled($item))
                ->implode(value: ' ')
        );
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->getFirstMediaUrl(collectionName: static::MEDIA_COLLECTION_PHOTO) ?:
                Cache::rememberForever(
                    'candidate-photo-' . str($this->full_name)->squish()->kebab()->toString(),
                    fn () => 'data:image/svg+xml;base64,' .
                        base64_encode(
                            app(InitialAvatar::class)
                                ->background(Rgb::fromString('rgb(' . FilamentColor::getColors()['info'][600] . ')')->toHex())
                                ->name($this->full_name)
                                ->color('#FFF')
                                ->rounded()
                                ->generateSvg()
                                ->toXMLString()
                        )
                ),
        );
    }

    protected function symbolUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $this->getFirstMediaUrl(collectionName: static::MEDIA_COLLECTION_SYMBOL) ?:
                Cache::rememberForever(
                    'candidate-symbol-' . str($this->sort)->squish()->kebab()->toString(),
                    fn () => 'data:image/svg+xml;base64,' .
                        base64_encode(
                            app(InitialAvatar::class)
                                ->background('#000')
                                ->color('#FFF')
                                ->fontSize(0.7)
                                ->name($this->sort)
                                ->generateSvg()
                                ->toXMLString()
                        )
                ),
        );
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(related: Position::class);
    }

    public function candidateGroup(): BelongsTo
    {
        return $this->belongsTo(related: CandidateGroup::class);
    }

    public function elector(): BelongsTo
    {
        return $this->belongsTo(related: Elector::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(
            scope: 'disabled',
            implementation: fn (Builder $builder) => $builder->where('disabled', false),
        );
    }

    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('position_id', $this->position_id);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl(collectionName: static::MEDIA_COLLECTION_PHOTO);
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
    }

    public function getFallbackLocale()
    {
        return $this->locales()[0] ?? config('app.locale');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(name: static::MEDIA_COLLECTION_PHOTO)
            ->singleFile()
            ->withResponsiveImages();

        $this
            ->addMediaCollection(name: static::MEDIA_COLLECTION_SYMBOL)
            ->singleFile()
            ->withResponsiveImages();
    }
}
