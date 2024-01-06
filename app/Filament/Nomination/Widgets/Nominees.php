<?php

namespace App\Filament\Nomination\Widgets;

use App\Enums\NomineeScrutinyStatus;
use App\Models\Elector;
use App\Models\Nominator;
use App\Models\Nominee;
use Filament\Facades\Filament;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

class Nominees extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        /** @var Elector $elector */
        $elector = Filament::auth()->user();

        return $table
            ->heading(heading: null)
            ->query(
                Nominee::query()
                    ->with(relations: ['proposer'])
                    ->whereBelongsTo(related: $elector)
                    ->orWhereHas(
                        relation: 'nominators',
                        callback: fn (Builder $query): Builder => $query->whereBelongsTo(related: $elector)
                    )
            )
            ->columns([
                Tables\Columns\TextColumn::make(name: '#')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make(name: 'position.name')
                    ->color(color: 'primary')
                    ->size(size: TextColumnSize::Large)
                    ->weight(weight: FontWeight::Bold),

                Tables\Columns\TextColumn::make(name: 'membership_number')
                    ->description(description: fn (Nominee $nominee): ?string => $nominee->full_name)
                    ->icon(icon: fn (Nominee $nominee): ?string => $nominee->status->getIcon())
                    ->iconColor(color: fn (Nominee $nominee): ?string => $nominee->status->getColor())
                    ->label(label: 'Nominee'),

                Tables\Columns\TextColumn::make(name: 'proposer')
                    ->description(description: fn (?Nominator $state): ?string => $state?->full_name)
                    ->formatStateUsing(callback: fn (?Nominator $state): string => $state?->membership_number)
                    ->icon(icon: fn (?Nominator $state): ?string => $state?->status->getIcon())
                    ->iconColor(color: fn (?Nominator $state): ?string => $state?->status->getColor()),

                Tables\Columns\TextColumn::make(name: 'seconders')
                    ->formatStateUsing(callback: fn (Nominator $state): string => $state->display_name)
                    ->icon(icon: fn (Nominator $state): ?string => $state->status->getIcon())
                    ->iconColor(color: fn (Nominator $state): ?string => $state->status->getColor())
                    ->listWithLineBreaks()
                    ->size(size: Tables\Columns\TextColumn\TextColumnSize::Small)
                    ->wrap(),

                Tables\Columns\TextColumn::make(name: 'scrutiny_status')
                    ->badge()
                    ->description(description: fn (Nominee $nominee): ?string => $nominee->scrutinised_at?->diffForHumans())
                    ->icon(icon: fn (NomineeScrutinyStatus $state): ?string => $state->getIcon())
                    ->color(color: fn (NomineeScrutinyStatus $state): ?string => $state->getColor())
                    ->label(label: 'Scrutiny'),
            ]);
    }
}
