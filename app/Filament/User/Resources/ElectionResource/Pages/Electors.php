<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionCollaboratorPermission;
use App\Enums\ElectionSetupStep;
use App\Events\Election\ElectorImportCompleted;
use App\Filament\Base\Contracts\HasElection;
use App\Filament\User\Resources\ElectionResource;
use App\Filament\User\Resources\ElectionResource\Widgets\ElectorDataImportProgress;
use App\Filament\User\Resources\ElectorResource;
use App\Models\Election;
use App\Models\Elector;
use Database\Factories\ElectorFactory;
use Filament\Actions\Action;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Actions\ImportAction as TableImportAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Electors extends ElectionPage implements HasTable
{
    use InteractsWithRelationshipTable;

    protected static string $view = 'filament.user.resources.election-resource.pages.electors';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';

    protected function getListeners(): array
    {
        return [
            ...parent::getListeners(),

            'echo-private:elections.' . $this->getElection()->id . ',.' . ElectorImportCompleted::getBroadcastName() => 'notifyImportCompletion',
        ];
    }

    public static function getRelationshipName(): string
    {
        return 'electors';
    }

    public function getOwnerRecord(): Election
    {
        return $this->getElection();
    }

    public function getCurrentStep(): ?ElectionSetupStep
    {
        return ElectionSetupStep::Electors;
    }

    public function hasReadAccess(): bool
    {
        return $this->isOwner()
            || $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->electors !== ElectionCollaboratorPermission::NoAccess;
    }

    public function hasFullAccess(): bool
    {
        return $this->isOwner()
            || $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->electors === ElectionCollaboratorPermission::FullAccess;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.user.election-resource.pages.electors.navigation_label');
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
                    ->authorize(abilities: fn (self $livewire): bool => static::can(action: 'deleteAnyElector', election: $livewire->getElection()))
                    ->visible(condition: $this->hasFullAccess()),

                $this->getNotifyVotingInstructionsBulkAction(),

                $this->getSendBallotLinkBulkAction(),
            ])
            ->emptyStateActions(actions: [
                $this->getCreateAction(),
            ])
            ->headerActions(actions: [
                $this->getImportAction(),

                $this->getCreateAction(),

                ActionGroup::make(actions: [
                    $this->getGenerateShortCodesAction(),

                    $this->getGenerateDummyElectorsAction(),
                ]),
            ])
            ->modifyQueryUsing(callback: fn (Builder $query): Builder => $query->with(['event', 'ballot']));
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ...$this->canShowStats() ? [ElectionResource\Widgets\ElectionStatsOverview::class] : [],

            ...$this->getImportProgressWidgets(),
        ];
    }

    protected function getImportProgressWidgets(): array
    {
        if (! $this->getElection()->is_draft) {
            return [];
        }

        return $this->getElection()
            ->electorImports()
            ->whereNull(columns: 'completed_at')
            ->get()
            ->map(callback: fn (Import $import) => ElectorDataImportProgress::make(['import' => $import]))
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),

            $this->getNextPageAction(),

            \Filament\Actions\ActionGroup::make(actions: [
                $this->getCollaboratorsPageAction(),
            ])->dropdownPlacement(placement: 'bottom-end'),
        ];
    }

    protected function getNextPageAction(): Action
    {
        return Action::make(name: 'nextPage')
            ->authorize(abilities: fn (self $livewire) => BallotSetup::canAccessPage(election: $livewire->getElection()))
            ->icon(icon: 'heroicon-s-chevron-double-right')
            ->label(label: __('filament.user.election-resource.pages.electors.actions.next.label'))
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
            ->visible(condition: $this->canDelete());
    }

    protected function getSendBallotLinkAction(): TableAction
    {
        return TableAction::make(name: 'sendBallotLink')
            ->authorize(abilities: 'sendBallotLink')
            ->requiresConfirmation()
            ->action(action: function (HasElection $livewire, Elector $elector, TableAction $action) {
                $elector->sendBallotLink(election: $livewire->getElection());

                $action->success();
            })
            ->icon(icon: 'heroicon-m-bell-alert')
            ->iconButton()
            ->label(label: __('filament.user.election-resource.pages.electors.actions.send_ballot_link.label'))
            ->successNotification(
                notification: fn (Notification $notification) => $notification
                    ->title(title: __('filament.user.election-resource.pages.electors.actions.send_ballot_link.success_notification.title'))
                    ->body(body: __('filament.user.election-resource.pages.electors.actions.send_ballot_link.success_notification.body'))
            )
            ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->preference?->isBallotLinkBlastNeeded());
    }

    protected function getSendBallotLinkBulkAction(): BulkAction
    {
        return BulkAction::make(name: 'sendBallotLinkBulk')
            ->authorize(abilities: fn (self $livewire): bool => static::can(action: 'sendBallotLinkBulk', election: $livewire->getElection()))
            ->requiresConfirmation()
            ->action(action: function (BulkAction $action, Collection $collection, HasElection $livewire) {
                $election = $livewire->getElection();

                $collection->each(
                    callback: function (Elector $elector) use ($election) {
                        if (! $elector->ballot?->isVoted()) {
                            $elector->sendBallotLink(election: $election);
                        }
                    }
                );

                $action->success();
            })
            ->icon(icon: 'heroicon-m-bell-alert')
            ->label(label: __('filament.user.election-resource.pages.electors.bulk_actions.send_ballot_link.label'))
            ->successNotification(
                notification: fn (Notification $notification) => $notification
                    ->title(title: __('filament.user.election-resource.pages.electors.bulk_actions.send_ballot_link.success_notification.title'))
                    ->body(body: __('filament.user.election-resource.pages.electors.bulk_actions.send_ballot_link.success_notification.body'))
            )
            ->visible(condition: fn (self $livewire): bool => $livewire->hasFullAccess() && $livewire->getElection()->preference?->isBallotLinkBlastNeeded());
    }

    public function getNotifyVotingInstructionsBulkAction()
    {
        return BulkAction::make(name: 'notifyVotingInstructionsBulk')
            ->authorize(abilities: fn (self $livewire): bool => static::can(action: 'notifyVotingInstructionsBulk', election: $livewire->getElection()))
            ->requiresConfirmation()
            ->action(action: function (BulkAction $action, Collection $collection) {
                $collection->each(
                    callback: function (Elector $elector) {
                        if (! $elector->ballot?->isVoted()) {
                            $elector->notifyVotingInstructions();
                        }
                    }
                );

                $action->success();
            })
            ->hidden()
            ->icon(icon: 'heroicon-m-bell-alert')
            ->label(label: 'Notify Voting Instructions')
            ->successNotification(
                notification: fn (Notification $notification) => $notification
                    ->title(title: 'Voting Instructions Sent')
                    ->body(body: 'Voting instructions have been sent to selected electors who have not yet voted.')
            );
    }

    public function getGenerateShortCodesAction(): TableAction
    {
        return TableAction::make(name: 'generateShortCodes')
            ->requiresConfirmation()
            ->action(action: function (TableAction $action, self $livewire) {
                $livewire->getElection()->electors()->whereNull('short_code')
                    ->cursor()
                    ->each(
                        callback: function (Elector $elector) {
                            $elector->short_code;
                        }
                    );

                $action->success();
            })
            ->hidden()
            ->icon(icon: 'heroicon-m-key')
            ->label(label: 'Generate Short Codes')
            ->successNotification(
                notification: fn (Notification $notification) => $notification
                    ->title(title: 'Short Codes Generated')
                    ->body(body: 'Short codes have been generated for electors who do not have one.')
            )
            ->visible(
                condition: $this->getElection()->is_published &&
                $this->getElection()->electors()->whereNull('short_code')->count() &&
                $this->hasFullAccess()
            );
    }

    public function getGenerateDummyElectorsAction(): TableAction
    {
        return TableAction::make('generateDummyElectors')
            ->authorize(auth()->user()->hasAdminRole())
            ->requiresConfirmation()
            ->action(function (self $livewire, TableAction $action, array $data) {
                Elector::factory($data['count'])
                    ->for($livewire->getElection(), 'event')
                    ->when($data['with_name'], fn (ElectorFactory $factory) => $factory->withName())
                    ->when($data['with_email'], fn (ElectorFactory $factory) => $factory->withEmail())
                    ->when($data['with_phone'], fn (ElectorFactory $factory) => $factory->withPhone())
                    ->create();

                $action->success();
            })
            ->form([
                TextInput::make('count')
                    ->default(10)
                    ->integer()
                    ->maxValue(99999)
                    ->minValue(1)
                    ->required(),

                Toggle::make('with_name')
                    ->default(true),

                Toggle::make('with_email')
                    ->default(true),

                Toggle::make('with_phone')
                    ->default(true),
            ])
            ->successNotificationTitle('Generated successfully');
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
        return $this->hasFullAccess() && static::can(action: 'createElector', election: $this->getElection());
    }

    protected function canImport(): bool
    {
        return $this->hasFullAccess()
            && static::can(action: 'importElector', election: $this->getElection())
            && $this->getElection()->electorImports()->whereNull('completed_at')->count() < 1;
    }

    protected function canEdit(): bool
    {
        return $this->hasFullAccess() && static::can(action: 'updateAnyElector', election: $this->getElection());
    }

    protected function canDelete(): bool
    {
        return $this->hasFullAccess() && static::can(action: 'deleteAnyElector', election: $this->getElection());
    }
}
