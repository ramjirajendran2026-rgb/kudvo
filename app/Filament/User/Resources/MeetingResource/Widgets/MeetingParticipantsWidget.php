<?php

namespace App\Filament\User\Resources\MeetingResource\Widgets;

use App\Filament\User\Resources\ParticipantResource;
use App\Models\Meeting;
use App\Models\Participant;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MeetingParticipantsWidget extends BaseWidget
{
    public Meeting $record;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return ParticipantResource::table($table)
            ->hiddenFilterIndicators()
            ->filters([
                TernaryFilter::make('voted_at')
                    ->default()
                    ->label('')
                    ->nullable()
                    ->placeholder('All participants')
                    ->trueLabel('Voted')
                    ->falseLabel('Non-Voted'),
            ], FiltersLayout::AboveContent)
            ->heading(null)
            ->poll('30s')
            ->query(fn () => Participant::query()->where('meeting_id', $this->record->getKey()));
    }

    public function getRecord(): Meeting
    {
        return $this->record;
    }
}
