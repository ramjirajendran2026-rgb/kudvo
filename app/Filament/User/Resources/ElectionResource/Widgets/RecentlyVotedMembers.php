<?php

namespace App\Filament\User\Resources\ElectionResource\Widgets;

use App\Models\Ballot;
use App\Models\Election;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentlyVotedMembers extends BaseWidget
{
    public Election $election;

    public function table(Table $table): Table
    {
        return $table
            ->description(description: fn (): string => "Updated at ".now(tz: $this->election->timezone)->format(format: Table::$defaultDateTimeDisplayFormat))
            ->paginated(condition: false)
            ->poll(interval: $this->election->is_open ? '10s': null)
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
                    ->latest(column: 'voted_at')
                    ->limit(value: 10)
            )
            ->columns([
                Tables\Columns\TextColumn::make(name: 'elector.display_name')
                    ->wrap(),

                Tables\Columns\TextColumn::make(name: 'voted_at')
                    ->dateTime(
                        timezone: $this->election->timezone,
                    ),
            ]);
    }
}
