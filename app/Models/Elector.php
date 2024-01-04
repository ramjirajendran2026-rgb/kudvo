<?php

namespace App\Models;

use App\Facades\Kudvo;
use App\Filament\NominationPanel;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

class Elector extends Model implements AuthenticatableContract, AuthorizableContract, FilamentUser, HasName
{
    use Authenticatable;
    use Authorizable;
    use HasFactory;
    use HasUuids;
    use Notifiable;

    protected $fillable = [
        'membership_number',
        'title',
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'groups',
        'event_id',
        'event_type',
    ];

    protected $casts = [
        'event_id' => 'int',
    ];

    public function event(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::saving(callback: function (Elector $elector) {
            if (!is_null($elector->groups)) {
                $elector->groups = collect(value: explode(separator: ',', string: $elector->groups))
                    ->map(callback: fn (string $item): string => trim(string: $item))
                    ->unique()
                    ->sort()
                    ->implode(value: ',') ?: null;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $panel instanceof NominationPanel) {
            return false;
        }

        return $this->event->is(model: Kudvo::getNomination());
    }

    public function getFilamentName(): string
    {
        return $this->full_name ?: $this->membership_number;
    }
}
