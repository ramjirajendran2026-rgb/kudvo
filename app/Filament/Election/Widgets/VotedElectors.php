<?php

namespace App\Filament\Election\Widgets;

use App\Models\Ballot;
use App\Models\Election;
use App\Models\Elector;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class VotedElectors extends BaseWidget
{
    public Election $election;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Voted Electors';

    public function table(Table $table): Table
    {
        return $table
            ->description(description: fn (): string => "Updated at ".now(tz: $this->election->timezone)->format(format: Table::$defaultDateTimeDisplayFormat))
            ->poll()
            ->query(
                Ballot::query()
                    ->live()
                    ->voted()
                    ->whereHas(
                        relation: 'elector',
                        callback: fn (Builder $query) => $query
                            ->whereMorphedTo(
                                relation: 'event',
                                model: $this->election,
                            )
                    )
            )
            ->defaultSort(column: 'voted_at', direction: 'desc')
            ->columns([
                Tables\Columns\TextColumn::make(name: 'elector.membership_number')
                    ->label(label: 'Code')
                    ->searchable(),

                Tables\Columns\TextColumn::make(name: 'elector.display_name')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make(name: 'voted_at')
                    ->dateTime(
                        timezone: $this->election->timezone,
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make(name: 'type')
                    ->badge()
                    ->label(label: 'Method')
                    ->visible(condition: $this->election->isBoothVotingEnabled()),
            ]);
    }
}
