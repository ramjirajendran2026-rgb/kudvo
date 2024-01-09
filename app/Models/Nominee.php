<?php

namespace App\Models;

use App\Enums\NomineeScrutinyStatus;
use App\Enums\NomineeStatus;
use App\Events\NomineeAccepted;
use App\Events\NomineeDeclined;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Nominee extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const ATTACHMENTS_COLLECTION_NAME = 'attachments';

    public const BIO_COLLECTION_NAME = 'bio';

    public const PHOTO_COLLECTION_NAME = 'photo';

    protected $fillable = [
        'membership_number',
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'self_nomination',
        'status',
        'scrutiny_status',
        'remarks',
        'decided_at',
        'scrutinised_at',
        'withdrawn_at',
        'position_id',
        'elector_id',
        'scrutiniser_id',
    ];

    protected $casts = [
        'self_nomination' => 'bool',
        'status' => NomineeStatus::class,
        'scrutiny_status' => NomineeScrutinyStatus::class,
        'decided_at' => 'datetime',
        'scrutinised_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'position_id' => 'int',
        'elector_id' => 'int',
        'scrutiniser_id' => 'int',
    ];

    protected $attributes = [
        'status' => NomineeStatus::PENDING,
        'scrutiny_status' => NomineeScrutinyStatus::PENDING,
    ];

    protected $appends = [
        'display_name',
    ];

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->membership_number.
                (filled(value: $this->full_name) ? ' ('.$this->full_name.')' : ''),
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

    public function nominators(): HasMany
    {
        return $this->hasMany(related: Nominator::class);
    }

    public function proposer(): HasOne
    {
        return $this->hasOne(related: Nominator::class)
            ->oldestOfMany();
    }

    public function seconders(): HasMany
    {
        return $this
            ->nominators()
            ->oldest()
            ->take(value: 9999999999)
            ->skip(value: 1);
    }

    public function scrutiniser(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }

    public function accept(): static
    {
        $this->status = NomineeStatus::ACCEPTED;
        $this->decided_at = now();

        NomineeAccepted::dispatchIf($this->save(), $this);

        return $this;
    }

    public function decline(): static
    {
        $this->status = NomineeStatus::DECLINED;
        $this->decided_at = now();

        NomineeDeclined::dispatchIf($this->save(), $this);

        return $this;
    }

    public function isPending(): bool
    {
        return $this->status == NomineeStatus::PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status == NomineeStatus::ACCEPTED;
    }

    public function isDeclined(): bool
    {
        return $this->status == NomineeStatus::DECLINED;
    }
}
