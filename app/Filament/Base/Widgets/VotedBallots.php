<?php

namespace App\Filament\Base\Widgets;

use App\Facades\Kudvo;
use App\Models\Ballot;
use App\Models\Election;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class VotedBallots extends BaseWidget
{
    public Election $election;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Voted Electors';

    public function table(Table $table): Table
    {
        return $table
            ->description(description: fn (): string => "Updated at ".now(tz: $this->election->timezone)->format(format: Table::$defaultDateTimeDisplayFormat))
            ->poll(interval: fn () => (Kudvo::isBoothDevice() && $this->election->is_booth_open) || $this->election->is_open ? '10s' : null)
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
                    ->searchable()
                    ->wrapHeader(),

                Tables\Columns\TextColumn::make(name: 'elector.display_name')
                    ->searchable(condition: 'full_name')
                    ->wrap()
                    ->wrapHeader(),

                Tables\Columns\TextColumn::make(name: 'voted_at')
                    ->dateTime(
                        timezone: $this->election->timezone,
                    )
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make(name: 'type')
                    ->badge()
                    ->label(label: 'Method')
                    ->visible(condition: $this->election->isBoothVotingEnabled()),
            ]);
    }
}
