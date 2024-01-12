<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Candidate extends Model implements HasMedia, HasName, HasAvatar
{
    use HasUuids;
    use InteractsWithMedia;

    public const MEDIA_COLLECTION_ATTACHMENTS = 'attachments';

    public const MEDIA_COLLECTION_BIO = 'bio';

    public const MEDIA_COLLECTION_PHOTO = 'photo';

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

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl(collectionName: static::MEDIA_COLLECTION_PHOTO);
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
    }
}
