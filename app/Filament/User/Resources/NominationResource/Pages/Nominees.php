<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Enums\NomineeScrutinyStatus;
use App\Models\Nomination;
use App\Models\Nominator;
use App\Models\Nominee;
use Exception;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class Nominees extends NominationPage implements HasTable
{
    use Concerns\InteractsWithNomination;
    use InteractsWithRelationshipTable;

    protected static string $view = 'filament.resources.nomination-resource.pages.nominees';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->loadDefaultActiveTab();
    }

    public static function getRelationshipName(): string
    {
        return 'nominees';
    }

    public function getOwnerRecord(): Nomination
    {
        return $this->getNomination();
    }

    public static function getNavigationLabel(): string
    {
        return 'Nominees';
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->actions(actions: [
                ActionGroup::make(actions: [
                    $this->getApproveAction(),

                    $this->getRejectAction(),
                ]),
            ])
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
                            modifyQueryUsing: fn (Builder $query, self $livewire): Builder => $query
                                ->whereMorphedTo(
                                    relation: 'event',
                                    model: $livewire->getNomination(),
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

    protected function getApproveAction()
    {
        return Action::make(name: 'approve')
            ->action(action: function (Nominee $nominee, Action $action): void {
                $nominee->approve();

                $action->success();
            })
            ->requiresConfirmation()
            ->color(color: NomineeScrutinyStatus::APPROVED->getColor())
            ->icon(icon: NomineeScrutinyStatus::APPROVED->getIcon())
            ->successNotificationTitle(title: 'Approved')
            ->visible(condition: fn (self $livewire, Nominee $nominee): bool => $livewire->canApprove(nominee: $nominee));
    }

    protected function getRejectAction()
    {
        return Action::make(name: 'reject')
            ->action(action: function (Nominee $nominee, Action $action): void {
                $nominee->reject();

                $action->success();
            })
            ->requiresConfirmation()
            ->color(color: NomineeScrutinyStatus::REJECTED->getColor())
            ->icon(icon: NomineeScrutinyStatus::REJECTED->getIcon())
            ->successNotificationTitle(title: 'Rejected')
            ->visible(condition: fn (self $livewire, Nominee $nominee): bool => $livewire->canReject(nominee: $nominee));
    }

    protected function canApprove(?Nominee $nominee = null): bool
    {
        if (filled(value: $nominee) && ! $nominee->isScrutinyPending()) {
            return false;
        }

        return static::can(action: 'approveAnyNominee', nomination: $this->getNomination());
    }

    protected function canReject(?Nominee $nominee = null): bool
    {
        if (filled(value: $nominee) && ! $nominee->isScrutinyPending()) {
            return false;
        }

        return static::can(action: 'rejectAnyNominee', nomination: $this->getNomination());
    }
}
