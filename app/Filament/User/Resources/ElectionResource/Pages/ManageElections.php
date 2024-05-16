<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionStatus;
use App\Filament\User\Resources\ElectionResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\ManageRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ManageElections extends ManageRecords
{
    protected static string $resource = ElectionResource::class;

    protected ?string $heading = '';

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(label: __('app.all'))
                ->badge(
                    badge: $this->getTableQuery()->count()
                ),

            ...Arr::mapWithKeys(
                array: ElectionStatus::cases(),
                callback: fn(ElectionStatus $case) => [
                    $case->value => Tab::make(label: $case->getLabel())
                        ->badge(
                            badge: $this->getTableQuery()->scopes(scopes: Arr::wrap(value: $case->getScopes()))->count()
                        )
                        ->badgeColor(color: $case->getColor())
                        ->icon(icon: $case->getIcon())
                        ->modifyQueryUsing(
                            callback: fn (Builder $query): Builder => $query->scopes(scopes: Arr::wrap(value: $case->getScopes()))
                        )
                ]
            )
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()
            ->where(
                column: fn(Builder $query) => $query->whereBelongsTo(related: Filament::auth()->user(), relationshipName: 'owner')
                    ->orWhereHas(relation: 'collaborators', callback: fn (Builder $query) => $query->whereKey(Filament::auth()->id()))
            );
    }
}
