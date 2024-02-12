<?php

namespace App\Models;

use App\Enums\NominatorStatus;
use App\Events\NominatorAccepted;
use App\Events\NominatorDeclined;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Nominator extends Model
{
    protected $fillable = [
        'membership_number',
        'title',
        'first_name',
        'last_name',
        'email',
        'phone',
        'status',
        'decided_at',
        'nominee_id',
        'elector_id',
    ];

    protected $casts = [
        'status' => NominatorStatus::class,
        'decided_at' => 'datetime',
        'nominee_id' => 'int',
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

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(related: Nominee::class);
    }

    public function elector(): BelongsTo
    {
        return $this->belongsTo(related: Elector::class);
    }

    public function isAccepted(): bool
    {
        return $this->status == NominatorStatus::ACCEPTED;
    }

    public function isPending(): bool
    {
        return $this->status == NominatorStatus::PENDING;
    }

    public function isDeclined(): bool
    {
        return $this->status == NominatorStatus::DECLINED;
    }

    public function accept(): static
    {
        $this->status = NominatorStatus::ACCEPTED;
        $this->decided_at = now();

        NominatorAccepted::dispatchIf($this->save(), $this);

        return $this;
    }

    public function decline(): static
    {
        $this->status = NominatorStatus::DECLINED;
        $this->decided_at = now();

        NominatorDeclined::dispatchIf($this->save(), $this);

        return $this;
    }
}
