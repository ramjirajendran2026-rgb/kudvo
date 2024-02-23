<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Filament\Contracts\HasElection;
use App\Filament\User\Resources\ElectionResource;
use App\Filament\User\Resources\ElectorResource;
use App\Models\Election;
use App\Models\Elector;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Actions\ImportAction as TableImportAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class Electors extends ElectionPage implements HasTable
{
    use InteractsWithRelationshipTable;

    protected static string $view = 'filament.user.resources.election-resource.pages.electors';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';

    public static function getRelationshipName(): string
    {
        return 'electors';
    }

    public function getOwnerRecord(): Election
    {
        return $this->getElection();
    }

    public function form(Form $form): Form
    {
        return ElectorResource::form(form: $form);
    }

    public function table(Table $table): Table
    {
        return ElectorResource::table(table: $table)
            ->actions(actions: [
                $this->getEditAction(),

                $this->getDeleteAction(),

                $this->getSendBallotLinkAction(),
            ])
            ->groupedBulkActions(actions: [
                ElectorResource::getBulkDeleteAction()
                    ->authorize(abilities: fn (self $livewire): bool => static::can(action: 'deleteAnyElector', election: $livewire->getElection())),

                $this->getSendBallotLinkBulkAction(),
            ])
            ->emptyStateActions(actions: [
                $this->getCreateAction(),
            ])
            ->headerActions(actions: [
                $this->getImportAction(),

                $this->getCreateAction(),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return match (true) {
            $this->canShowStats() => [
                ElectionResource\Widgets\ElectionStatsOverview::class,
            ],
            default => [],
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getNextPageAction(),
        ];
    }

    protected function getNextPageAction(): Action
    {
        return Action::make(name: 'nextPage')
            ->authorize(abilities: fn (self $livewire) => BallotSetup::canAccessPage(election: $livewire->getElection()))
            ->icon(icon: 'heroicon-s-chevron-double-right')
            ->label(label: 'Next')
            ->outlined()
            ->url(url: BallotSetup::getUrl(parameters: [$this->getElection()]));
    }

    protected function getImportAction(): TableImportAction
    {
        return ElectorResource::getTableImportAction()
            ->options(options: fn (self $livewire): array => [
                'event_type' => Election::class,
                'event_id' => $livewire->getElection()->getKey(),
            ])
            ->visible(condition: $this->canImport());
    }

    protected function getCreateAction(): TableCreateAction
    {
        return ElectorResource::getTableCreateAction()
            ->visible(condition: $this->canCreate());
    }

    protected function getEditAction(): TableEditAction
    {
        return ElectorResource::getTableEditAction()
            ->authorize(abilities: 'update')
            ->visible(condition: $this->canEdit());
    }

    protected function getDeleteAction(): TableDeleteAction
    {
        return ElectorResource::getTableDeleteAction()
            ->visible(condition:$this->canDelete());
    }

    protected function getSendBallotLinkAction(): TableAction
    {
        return TableAction::make(name: 'sendBallotLink')
            ->authorize(abilities: 'sendBallotLink')
            ->requiresConfirmation()
            ->action(action: function (HasElection $livewire, Elector $elector, TableAction $action) {
                $elector->sendBallotLink(election: $livewire->getElection(), now: true);

                $action->success();
            })
            ->icon(icon: 'heroicon-m-bell-alert')
            ->iconButton()
            ->successNotification(
                notification: fn (Notification $notification) => $notification
                    ->title(title: 'Ballot Link Sent')
                    ->body(body: 'The ballot link has been sent to the elector.')
            );
    }

    protected function getSendBallotLinkBulkAction(): BulkAction
    {
        return BulkAction::make(name: 'sendBallotLinkBulk')
            ->authorize(abilities: fn (self $livewire): bool => static::can(action: 'sendBallotLinkBulk', election: $livewire->getElection()))
            ->requiresConfirmation()
            ->action(action: function (BulkAction $action, Collection $collection) {
                $collection->each(
                    callback: function (Elector $elector) {
                        if (!$elector->ballot?->isVoted()) {
                            $elector->sendBallotLink();
                        }
                    }
                );

                $action->success();
            })
            ->icon(icon: 'heroicon-m-bell-alert')
            ->label(label: 'Send Ballot Links')
            ->successNotification(
                notification: fn (Notification $notification) => $notification
                    ->title(title: 'Ballot Links Sent')
                    ->body(body: 'Ballot links have been sent to selected electors who have not yet voted.')
            );
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage(election: $election) &&
            static::can(action: 'viewAnyElector', election: $election);
    }

    protected function canShowStats(): bool
    {
        $election = $this->getElection();

        return $election->is_open ||
            $election->is_expired ||
            $election->is_closed ||
            $election->is_completed;
    }

    protected function canCreate(): bool
    {
        return static::can(action: 'createElector', election: $this->getElection());
    }

    protected function canImport(): bool
    {
        return static::can(action: 'importElector', election: $this->getElection());
    }

    protected function canEdit(): bool
    {
        return static::can(action: 'updateAnyElector', election: $this->getElection());
    }

    protected function canDelete(): bool
    {
        return static::can(action: 'deleteAnyElector', election: $this->getElection());
    }
}
