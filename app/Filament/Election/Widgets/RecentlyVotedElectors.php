<?php

namespace App\Filament\Election\Widgets;

use App\Models\Ballot;
use App\Models\Election;
use App\Models\Elector;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentlyVotedElectors extends BaseWidget
{
    public Election $election;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->description(description: fn (): string => "Updated at ".now(tz: $this->election->timezone)->format(format: Table::$defaultDateTimeDisplayFormat))
            ->poll()
            ->query(
                Elector::whereMorphedTo(relation: 'event', model: $this->election)
                    ->with(relations: 'ballot')
                    ->whereHas(
                        relation: 'ballot',
                        callback: fn (Builder $query) => $query->whereNotNull('voted_at'),
                    )
                    ->orderByDesc(column: 'ballot.voted_at'),
            )
            ->columns([
                Tables\Columns\TextColumn::make(name: 'membership_number')
                    ->label(label: 'Code'),

                Tables\Columns\TextColumn::make(name: 'full_name')
                    ->wrap(),

                Tables\Columns\TextColumn::make(name: 'ballot.voted_at')
                    ->dateTime(
                        timezone: $this->election->timezone,
                    ),

                Tables\Columns\TextColumn::make(name: 'type')
                    ->badge()
                    ->label(label: 'Method')
                    ->visible(condition: $this->election->isBoothVotingEnabled()),
            ]);
    }
}
