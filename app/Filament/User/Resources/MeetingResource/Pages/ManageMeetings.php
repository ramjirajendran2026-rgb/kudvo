<?php

namespace App\Filament\User\Resources\MeetingResource\Pages;

use App\Filament\User\Resources\MeetingResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageMeetings extends ManageRecords
{
    protected static string $resource = MeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'draft' => Tab::make('Draft')
                ->icon('heroicon-o-pencil-square')
                ->query(
                    fn (Builder $query) => $query->whereNull('cancelled_at')
                        ->whereNull('published_at')
                ),
            'published' => Tab::make('Published')
                ->icon('heroicon-o-rocket-launch')
                ->query(
                    fn (Builder $query) => $query->whereNull('cancelled_at')
                        ->whereNotNull(columns: 'published_at')
                        ->whereNull('voting_closed_at')
                        ->where('voting_starts_at', '>=', now())
                ),
            'voting_in_progress' => Tab::make('Voting in progress')
                ->icon('heroicon-o-chart-bar')
                ->query(
                    fn (Builder $query) => $query->whereNull('cancelled_at')
                        ->whereNotNull(columns: 'published_at')
                        ->whereNull('voting_closed_at')
                        ->where('voting_starts_at', '<=', now())
                ),
            'completed' => Tab::make('Completed')
                ->icon('heroicon-o-check-circle')
                ->query(
                    fn (Builder $query) => $query->whereNotNull('cancelled_at')
                        ->orWhere(
                            fn (Builder $query) => $query
                                ->whereNotNull(columns: 'published_at')
                                ->where('voting_closed_at', '<=', now())
                        )
                ),
        ];
    }
}
