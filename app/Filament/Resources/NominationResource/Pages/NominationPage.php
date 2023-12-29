<?php

namespace App\Filament\Resources\NominationResource\Pages;

use App\Filament\Resources\NominationResource;
use App\Models\Nomination;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;

/**
 * @property Nomination $nomination
 */
class NominationPage extends Page
{
    use InteractsWithRecord;

    protected static string $resource = NominationResource::class;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord(key: $record);

        $this->authorizeAccess();
    }

    public function unsetProps(string|array $properties): void
    {
        foreach (Arr::wrap($properties) as $property) {
            unset($this->{$property});
        }
    }

    #[Computed]
    public function nomination(): Nomination
    {
        return Nomination::withCount(relations: 'positions')
            ->findOrFail($this->getRecord()->getKey());
    }

    public static function can(string $action, Nomination $nomination): bool
    {
        return NominationResource::can(action: $action, record: $nomination);
    }

    public static function cannot(string $action, Nomination $nomination): bool
    {
        return ! static::can(action: $action, nomination: $nomination);
    }

    public static function canAccess(Nomination $nomination): bool
    {
        return NominationResource::canView(record: $nomination);
    }

    public function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        abort_unless(
            boolean: static::canAccess(nomination: $this->nomination),
            code: 403,
        );
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getHeading(): string|Htmlable
    {
        return $this->getRecordTitle();
    }
}
