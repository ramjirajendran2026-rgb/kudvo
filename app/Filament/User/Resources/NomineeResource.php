<?php

namespace App\Filament\User\Resources;

use App\Enums\NomineeScrutinyStatus;
use App\Filament\Base\Contracts\HasElection;
use App\Filament\Base\Contracts\HasNomination;
use App\Models\Nominator;
use App\Models\Nominee;
use Exception;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NomineeResource extends Resource
{
    protected static ?string $model = Nominee::class;

    protected static bool $isDiscovered = false;

    protected static ?string $recordTitleAttribute = 'membership_number';

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(name: '#')
                    ->rowIndex(),

                TextColumn::make(name: 'position.name')
                    ->color(color: 'primary')
                    ->size(size: TextColumnSize::Large)
                    ->weight(weight: FontWeight::Bold),

                TextColumn::make(name: 'membership_number')
                    ->description(description: fn (Nominee $nominee): ?string => $nominee->full_name)
                    ->icon(icon: fn (Nominee $nominee): ?string => $nominee->status->getIcon())
                    ->iconColor(color: fn (Nominee $nominee): ?string => $nominee->status->getColor())
                    ->label(label: 'Nominee'),

                TextColumn::make(name: 'proposer')
                    ->description(description: fn (?Nominator $state): ?string => $state?->full_name)
                    ->formatStateUsing(callback: fn (?Nominator $state): string => $state?->membership_number)
                    ->icon(icon: fn (?Nominator $state): ?string => $state?->status->getIcon())
                    ->iconColor(color: fn (?Nominator $state): ?string => $state?->status->getColor()),

                TextColumn::make(name: 'seconders')
                    ->formatStateUsing(callback: fn (Nominator $state): string => $state->display_name)
                    ->icon(icon: fn (Nominator $state): ?string => $state->status->getIcon())
                    ->iconColor(color: fn (Nominator $state): ?string => $state->status->getColor())
                    ->listWithLineBreaks()
                    ->size(size: TextColumnSize::Small)
                    ->wrap(),

                TextColumn::make(name: 'scrutiny_status')
                    ->badge()
                    ->color(color: fn (NomineeScrutinyStatus $state): ?string => $state->getColor())
                    ->icon(icon: fn (NomineeScrutinyStatus $state): ?string => $state->getIcon())
                    ->label(label: 'Scrutiny'),
            ])
            ->filters(
                filters: [
                    SelectFilter::make(name: 'position')
                        ->preload()
                        ->relationship(
                            name: 'position',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query, HasElection|HasNomination $livewire): Builder => $query
                                ->whereMorphedTo(
                                    relation: 'event',
                                    model: match (true) {
                                        $livewire instanceof HasElection => $livewire->getElection(),
                                        $livewire instanceof HasNomination => $livewire->getNomination(),
                                    },
                                )
                        )
                        ->searchable(),

                    SelectFilter::make(name: 'scrutiny_status')
                        ->native(condition: false)
                        ->options(options: NomineeScrutinyStatus::options()),
                ],
            )
            ->filtersFormColumns(columns: 2)
            ->hiddenFilterIndicators();
    }

    public static function getApproveAction(): TableAction
    {
        return TableAction::make(name: 'approve')
            ->action(action: function (Nominee $nominee, TableAction $action): void {
                $nominee->approve();

                $action->success();
            })
            ->requiresConfirmation()
            ->color(color: NomineeScrutinyStatus::APPROVED->getColor())
            ->icon(icon: NomineeScrutinyStatus::APPROVED->getIcon())
            ->successNotificationTitle(title: 'Approved');
    }

    public static function getRejectAction()
    {
        return TableAction::make(name: 'reject')
            ->action(action: function (Nominee $nominee, TableAction $action): void {
                $nominee->reject();

                $action->success();
            })
            ->requiresConfirmation()
            ->color(color: NomineeScrutinyStatus::REJECTED->getColor())
            ->icon(icon: NomineeScrutinyStatus::REJECTED->getIcon())
            ->successNotificationTitle(title: 'Rejected');
    }
}
