<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionCollaboratorPermission;
use App\Events\Election\Booth\Activated;
use App\Events\Election\Booth\PrintBallot;
use App\Events\ElectorAssignedToBoothEvent;
use App\Events\ElectorCastedVoteInBoothEvent;
use App\Events\ElectorRevokedFromBoothEvent;
use App\Models\Election;
use App\Models\ElectionBoothToken;
use App\Models\Elector;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BoothTokens extends ElectionPage implements HasTable
{
    use InteractsWithRelationshipTable;

    protected static string $view = 'filament.user.resources.election-resource.pages.booth-tokens';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $activeNavigationIcon = 'heroicon-s-archive-box';

    protected function getListeners(): array
    {
        $listeners = parent::getListeners();

        $listeners['echo-private:elections.' . $this->getElection()->id . ',.' . Activated::getBroadcastName()] = '$refresh';
        $listeners['echo-private:elections.' . $this->getElection()->id . ',.' . ElectorAssignedToBoothEvent::getBroadcastName()] = '$refresh';
        $listeners['echo-private:elections.' . $this->getElection()->id . ',.' . ElectorRevokedFromBoothEvent::getBroadcastName()] = '$refresh';
        $listeners['echo-private:elections.' . $this->getElection()->id . ',.' . ElectorCastedVoteInBoothEvent::getBroadcastName()] = '$refresh';

        return $listeners;
    }

    public static function getRelationshipName(): string
    {
        return 'boothTokens';
    }

    public function getOwnerRecord(): Election
    {
        return $this->getElection();
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.user.election-resource.pages.booth_tokens.navigation_label');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                TextInput::make(name: 'name')
                    ->default(state: ElectionBoothToken::make(['election_id' => $this->getElection()->getKey()])->getHighestOrderNumber() + 1)
                    ->maxLength(length: 30),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->actions(actions: [
                EditAction::make('assign')
                    ->after(callback: fn (ElectionBoothToken $record) => broadcast(new ElectorAssignedToBoothEvent($record->getKey(), $record->current_elector_id))->toOthers())
                    ->form(form: [
                        Select::make(name: 'currentElector')
                            ->getOptionLabelFromRecordUsing(callback: fn (Elector $record) => "$record->membership_number - $record->display_name")
                            ->hiddenLabel()
                            ->placeholder(placeholder: 'Select an elector')
                            ->preload()
                            ->relationship(
                                titleAttribute: 'membership_number',
                                modifyQueryUsing: fn (Builder $query): Builder => $query
                                    ->where('event_type', Election::class)
                                    ->where('event_id', $this->getElection()->id)
                                    ->whereDoesntHave(relation: 'booth')
                                    ->whereDoesntHave(
                                        relation: 'ballot',
                                        callback: fn (Builder $query): Builder => $query->scopes(scopes: 'voted')
                                    ),
                            )
                            ->required()
                            ->searchable(condition: ['membership_number', 'full_name']),
                    ])
                    ->icon(icon: 'heroicon-s-user-plus')
                    ->iconButton()
                    ->label(label: 'Assign')
                    ->modalCancelAction(action: false)
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalHeading(heading: 'Assign Elector to Booth')
                    ->modalWidth(width: MaxWidth::Medium)
                    ->visible(
                        condition: fn (ElectionBoothToken $record): bool => $this->hasFullAccess()
                            && $record->isActivated()
                            && blank($record->current_elector_id)
                            && $this->getElection()->booth_preference?->login_by_admin
                    ),

                Action::make(name: 'revoke')
                    ->requiresConfirmation()
                    ->action(action: function (ElectionBoothToken $record, Action $action): void {
                        $record->update(attributes: ['current_elector_id' => null]);

                        $action->success();
                    })
                    ->after(callback: fn (ElectionBoothToken $record) => broadcast(new ElectorRevokedFromBoothEvent($record->getKey()))->toOthers())
                    ->icon(icon: 'heroicon-s-user-minus')
                    ->iconButton()
                    ->label(label: 'Revoke')
                    ->successNotificationTitle(title: 'Revoked')
                    ->visible(
                        condition: fn (ElectionBoothToken $record): bool => $this->hasFullAccess()
                            && filled($record->current_elector_id)
                            && $this->getElection()->booth_preference?->logout_by_admin
                    ),

                Action::make(name: 'print')
                    ->requiresConfirmation()
                    ->action(action: function (ElectionBoothToken $record, Action $action): void {
                        broadcast(new PrintBallot($record->getKey()))->toOthers();

                        $action->success();
                    })
                    ->icon(icon: 'heroicon-s-printer')
                    ->iconButton()
                    ->label(label: 'Print ballot')
                    ->successNotificationTitle(title: 'Submitted')
                    ->visible(
                        condition: fn (ElectionBoothToken $record): bool => $this->hasFullAccess()
                            && $record->currentElector?->ballot?->isVoted()
                            && $this->getElection()->booth_preference?->voted_ballot_print_by_admin
                    ),

                EditAction::make()
                    ->form(form: fn (Form $form): Form => $this->form(form: $form))
                    ->iconButton()
                    ->modalCancelAction(action: false)
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalWidth(width: MaxWidth::Medium)
                    ->visible(condition: fn (): bool => $this->hasFullAccess()),

                DeleteAction::make()
                    ->hidden(condition: fn (ElectionBoothToken $record): bool => $record->isActivated())
                    ->iconButton()
                    ->visible(condition: fn (): bool => $this->hasFullAccess()),

                ActionGroup::make(actions: [
                    Action::make(name: 'copyActivationLink')
                        ->alpineClickHandler(
                            handler: fn (ElectionBoothToken $record): string => 'window.navigator.clipboard.writeText("' . $record->getLink() . '");
                                $tooltip(\'Copied\', {theme: $store.theme,
                                    timeout: 2000,
                                })'
                        )
                        ->hidden(condition: fn (ElectionBoothToken $record): bool => $record->isActivated())
                        ->visible(condition: fn (): bool => $this->hasFullAccess()),
                ]),
            ])
            ->columns(components: [
                TextColumn::make(name: 'name')
                    ->alignCenter()
                    ->color(color: 'primary')
                    ->label(label: 'Booth')
                    ->size(size: TextColumn\TextColumnSize::Large),

                TextColumn::make(name: 'ballots_count')
                    ->alignCenter()
                    ->color(color: 'primary')
                    ->counts(relationships: 'ballots')
                    ->fontFamily(family: FontFamily::Mono)
                    ->label(label: 'Votes Polled')
                    ->size(size: TextColumn\TextColumnSize::Large),

                TextColumn::make(name: 'status')
                    ->badge(),

                TextColumn::make(name: 'currentElector.membership_number')
                    ->color(color: fn (ElectionBoothToken $record): ?string => $record->currentElector?->ballot?->isVoted() ? 'success' : null)
                    ->description(description: fn (ElectionBoothToken $record): ?string => $record->currentElector?->display_name)
                    ->icon(icon: fn (ElectionBoothToken $record): ?string => $record->currentElector?->ballot?->isVoted() ? 'heroicon-m-shield-check' : null)
                    ->iconPosition(iconPosition: IconPosition::After)
                    ->wrap(),
            ])
            ->defaultSort(column: 'id', direction: 'desc')
            ->headerActions(actions: [
                CreateAction::make()
                    ->form(form: fn (Form $form): Form => $this->form($form))
                    ->modalCancelAction(action: false)
                    ->modalWidth(width: MaxWidth::Medium)
                    ->visible(condition: fn (): bool => $this->hasFullAccess()),
            ])
            ->modelLabel(label: 'Booth')
            ->recordTitleAttribute(attribute: 'name');
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage(election: $election) &&
            static::can(action: 'viewAnyBoothToken', election: $election);
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::canAccessPage(election: $parameters['record']);
    }

    public function hasReadAccess(): bool
    {
        return $this->isOwner() ||
            $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->booth_tokens !== ElectionCollaboratorPermission::NoAccess;
    }

    public function hasFullAccess(): bool
    {
        return $this->isOwner() ||
            $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->booth_tokens === ElectionCollaboratorPermission::FullAccess;
    }
}
