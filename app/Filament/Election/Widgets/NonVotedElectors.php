<?php

namespace App\Filament\Election\Widgets;

use App\Models\Ballot;
use App\Models\Election;
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
            ->poll()
            ->query(
                $this->election->electors()
                    ->whereDoesntHave(
                        relation: 'ballot',
                        callback: fn (Builder $query) => $query->whereNotNull('voted_at'),
                    ),
            )
            ->columns([
                Tables\Columns\TextColumn::make(name: 'membership_number')
                    ->label(label: 'Code'),

                Tables\Columns\TextColumn::make(name: 'full_name')
                    ->wrap(),
            ]);
    }
}
