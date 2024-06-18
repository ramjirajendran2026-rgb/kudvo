<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionStatus;
use App\Filament\User\Resources\ElectionResource;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ManageElections extends ManageRecords
{
    use ManageRecords\Concerns\Translatable;

    protected static string $resource = ElectionResource::class;

    protected ?string $heading = '';

    public function getTabs(): array
    {
        return Arr::map(
            array: ElectionStatus::getTabs(),
            callback: fn (Tab $tab, string $key): Tab => $tab
                ->badge(badge: fn (Builder $query, Tab $component): int => $this->getTableQuery()->scopes(scopes: ElectionStatus::tryFrom($key)?->getScopes())->count())
        );
    }

    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()
            ->where(
                column: fn (Builder $query) => $query->whereBelongsTo(related: Filament::auth()->user(), relationshipName: 'owner')
                    ->orWhereHas(relation: 'collaborators', callback: fn (Builder $query) => $query->whereKey(Filament::auth()->id()))
            );
    }
}
