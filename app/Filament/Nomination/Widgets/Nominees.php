<?php

namespace App\Filament\Nomination\Widgets;

use App\Models\Elector;
use App\Models\Nominee;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class Nominees extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        /** @var Elector $elector */
        $elector = Filament::auth()->user();

        return $table
            ->heading(heading: 'My Requests')
            ->query(
                Nominee::query()
                    ->with(relations: ['proposer'])
                    ->whereBelongsTo(related: $elector)
                    ->orWhereHas(
                        relation: 'nominators',
                        callback: fn (Builder $query): Builder => $query->whereBelongsTo(related: $elector)
                    )
            )
            ->defaultGroup(group: 'position.name')
            ->columns([
                Tables\Columns\TextColumn::make(name: 'candidate')
                    ->description(description: fn (Nominee $nominee): ?string => $nominee->full_name)
                    ->getStateUsing(callback: fn (Nominee $nominee): ?string => $nominee->membership_number),

                Tables\Columns\TextColumn::make(name: 'nominee')
                    ->description(description: fn (Nominee $nominee): ?string => $nominee->self_nomination ? null : $nominee->proposer?->full_name)
                    ->getStateUsing(callback: fn (Nominee $nominee): ?string => $nominee->self_nomination ? 'Self' : $nominee->proposer?->membership_number),

                Tables\Columns\TextColumn::make(name: 'status')
                    ->badge(),
            ]);
    }
}
