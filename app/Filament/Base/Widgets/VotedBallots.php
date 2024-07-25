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

    public static function getHeading(): ?string
    {
        return __('filament.base.widgets.voted_ballots.heading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(heading: static::getHeading())
            ->description(description: fn (): string => __('filament.base.widgets.voted_ballots.table.description', ['timestamp' => now(tz: $this->election->timezone)->format(format: Table::$defaultDateTimeDisplayFormat)]))
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
                    ->label(label: __('filament.base.widgets.voted_ballots.table.membership_number.label'))
                    ->searchable()
                    ->wrapHeader(),

                Tables\Columns\TextColumn::make(name: 'elector.display_name')
                    ->label(label: __('filament.base.widgets.voted_ballots.table.elector.label'))
                    ->searchable(condition: 'full_name')
                    ->wrap()
                    ->wrapHeader(),

                Tables\Columns\TextColumn::make(name: 'voted_at')
                    ->dateTime(
                        timezone: $this->election->timezone,
                    )
                    ->label(label: __('filament.base.widgets.voted_ballots.table.voted_at.label'))
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make(name: 'type')
                    ->badge()
                    ->label(label: __('filament.base.widgets.voted_ballots.table.type.label'))
                    ->visible(condition: $this->election->isBoothVotingEnabled()),
            ]);
    }
}
