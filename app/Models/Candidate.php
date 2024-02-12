<?php

namespace App\Models;

use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Color\Rgb;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Candidate extends Model implements HasMedia, HasName, HasAvatar, Sortable
{
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
        'sort',
        'position_id',
        'elector_id',
    ];

    protected $casts = [
        'sort' => 'int',
        'position_id' => 'int',
        'elector_id' => 'int',
    ];

    protected $appends = [
        'display_name',
    ];

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => collect(value: [$this->title, $this->full_name])
                ->filter(callback: fn (?string $item): bool => filled($item))
                ->implode(value: ' ')
        );
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->getFilamentAvatarUrl() ?:
                app(abstract: UiAvatarsProvider::class)->get(record: $this),
        );
    }

    protected function symbolUrl(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => 'https://ui-avatars.com/api/?name=' .
                $this->sort .
                '&color=FFFFFF&background=' .
                str(Rgb::fromString('rgb(' . FilamentColor::getColors()['info'][800] . ')')->toHex())
                    ->after('#'),
        );
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(related: Position::class);
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
}
