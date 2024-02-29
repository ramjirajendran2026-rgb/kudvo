<?php

namespace App\Filament\Base\Widgets;

use App\Facades\Kudvo;
use App\Models\Election;
use App\Models\Elector;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class NonVotedElectors extends BaseWidget
{
    public Election $election;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->description(description: fn (): string => "Updated at ".now(tz: $this->election->timezone)->format(format: Table::$defaultDateTimeDisplayFormat))
            ->poll(interval: fn () => (Kudvo::isBoothDevice() && $this->election->is_booth_open) || $this->election->is_open ? '10s' : null)
            ->query(
                Elector::whereMorphedTo(relation: 'event', model: $this->election)
                    ->whereDoesntHave(
                        relation: 'ballot',
                        callback: fn (Builder $query) => $query->whereNotNull('voted_at'),
                    ),
            )
            ->columns([
                Tables\Columns\TextColumn::make(name: 'membership_number')
                    ->searchable()
                    ->wrapHeader(),

                Tables\Columns\TextColumn::make(name: 'display_name')
                    ->searchable(condition: 'full_name')
                    ->wrap()
                    ->wrapHeader(),
            ]);
    }
}
