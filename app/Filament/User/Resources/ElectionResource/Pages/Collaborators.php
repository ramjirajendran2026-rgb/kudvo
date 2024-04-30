<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Data\Election\CollaboratorPermissionsData;
use App\Enums\ElectionCollaboratorPermission;
use App\Models\Election;
use App\Models\ElectionUserInvitation;
use App\Models\User;
use App\Notifications\Election\CollaboratorInvitationNotification;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Collaborators extends ElectionPage implements HasTable
{
    use InteractsWithRelationshipTable;

    protected static string $view = 'filament.user.resources.election-resource.pages.collaborators';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $activeNavigationIcon = 'heroicon-s-users';

    protected static ?string $navigationGroup = 'Others';

    public static function getRelationshipName(): string
    {
        return 'collaborators';
    }

    public function getOwnerRecord(): Election
    {
        return $this->getElection();
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage($election) && $election->isOwner(Filament::auth()->user());
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns(components: [
                TextColumn::make(name: 'name')
                    ->label(label: 'Name'),

                TextColumn::make(name: 'email')
                    ->label(label: 'Email address'),

                TextColumn::make(name: 'collaboration.designation')
                    ->description(description: fn(User $user): ?string => $this->getOwnerRecord()->owner?->is(model: $user) ? 'Admin' : null)
                    ->label(label: 'Designation'),
            ])
            ->actions(actions: [
                EditAction::make()
                    ->form(
                        form: fn(Form $form): Form => $form
                            ->inlineLabel()
                            ->schema(components: [
                                TextInput::make(name: 'designation')
                                    ->label(label: 'Designation'),

                                Fieldset::make(label: 'Permissions')
                                    ->columns(columns: null)
                                    ->formatStateUsing(callback: fn($state): array => CollaboratorPermissionsData::from($state ?? [])->toArray())
                                    ->hidden(condition: fn(self $livewire, User $user): bool => $livewire->getOwnerRecord()->owner?->is(model: $user))
                                    ->statePath(path: 'permissions')
                                    ->schema(components: [
                                        Radio::make(name: 'preference')
                                            ->columns(columns: 3)
                                            ->label(label: 'Preference')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'electors')
                                            ->columns(columns: 3)
                                            ->label(label: 'Electors')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'ballot_setup')
                                            ->columns(columns: 3)
                                            ->label(label: 'Ballot setup')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'timing')
                                            ->columns(columns: 3)
                                            ->label(label: 'Timing')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'payment')
                                            ->columns(columns: 3)
                                            ->label(label: 'Payment')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'monitor_tokens')
                                            ->columns(columns: 3)
                                            ->label(label: 'Monitor tokens')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'elector_logs')
                                            ->columns(columns: 3)
                                            ->label(label: 'Elector logs')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),
                                    ]),
                            ])
                    )
                    ->iconButton()
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalWidth(width: MaxWidth::TwoExtraLarge),

                ActionGroup::make(actions: [
                    DetachAction::make()
                        ->hidden(condition: fn(self $livewire, User $user): bool => $livewire->getOwnerRecord()->owner?->is(model: $user))
                        ->label(label: 'Remove'),

                    Action::make(name: 'setAsAdmin')
                        ->requiresConfirmation()
                        ->action(action: function (User $user, Action $action): void {
                            $this->getOwnerRecord()
                                ->update(attributes: ['owner_id' => $user->id]);

                            $action->success();
                        })
                        ->successNotificationTitle(title: 'Admin changed successfully')
                        ->hidden(condition: fn(self $livewire, User $user): bool => $livewire->getOwnerRecord()->owner?->is(model: $user)),
                ]),
            ])
            ->headerActions(actions: [
                Action::make(name: 'inviteCollaborator')
                    ->action(action: function (array $data, Action $action): void {
                        $election = $this->getOwnerRecord();
                        $election->collaborationInvitations()
                            ->pending()
                            ->where('email', $data['email'])
                            ->get()
                            ->each(callback: fn(ElectionUserInvitation $invitation) => $invitation->delete());

                        $invitation = $election->collaborationInvitations()->create([
                            'email' => $data['email'],
                            'designation' => $data['designation'],
                            'permissions' => $data['permissions'],
                            'invitor_id' => Filament::auth()->id(),
                        ]);

                        $invitation->notify(new CollaboratorInvitationNotification(invitation: $invitation));

                        $action->success();
                    })
                    ->form(
                        form: fn(Form $form): Form => $form
                            ->inlineLabel()
                            ->schema(components: [
                                TextInput::make(name: 'email')
                                    ->email()
                                    ->label(label: 'Email address')
                                    ->notIn(
                                        values: fn(): array => $this->getOwnerRecord()
                                            ->collaborators()
                                            ->pluck('email')
                                            ->toArray()
                                    )
                                    ->validationMessages(messages: [
                                        'not_in' => 'The email address is already a collaborator.',
                                    ])
                                    ->required(),

                                TextInput::make(name: 'designation')
                                    ->label('Designation')
                                    ->maxLength(length: 100)
                                    ->placeholder(placeholder: 'e.g. Returning Officer / Election Officer')
                                    ->required(),

                                Fieldset::make(label: 'Permissions')
                                    ->columns(columns: null)
                                    ->default(state: CollaboratorPermissionsData::empty())
                                    ->hidden(condition: fn(self $livewire, User $user): bool => $livewire->getOwnerRecord()->owner?->is(model: $user))
                                    ->statePath(path: 'permissions')
                                    ->schema(components: [
                                        Radio::make(name: 'preference')
                                            ->columns(columns: 3)
                                            ->label(label: 'Preference')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'electors')
                                            ->columns(columns: 3)
                                            ->label(label: 'Electors')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'ballot_setup')
                                            ->columns(columns: 3)
                                            ->label(label: 'Ballot setup')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'timing')
                                            ->columns(columns: 3)
                                            ->label(label: 'Timing')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'payment')
                                            ->columns(columns: 3)
                                            ->label(label: 'Payment')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'monitor_tokens')
                                            ->columns(columns: 3)
                                            ->label(label: 'Monitor tokens')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),

                                        Radio::make(name: 'elector_logs')
                                            ->columns(columns: 3)
                                            ->label(label: 'Elector logs')
                                            ->options(options: ElectionCollaboratorPermission::class)
                                            ->required(),
                                    ]),
                            ])
                    )
                    ->icon(icon: 'heroicon-o-user-plus')
                    ->label(label: 'Invite collaborator')
                    ->modalCancelAction(action: false)
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalSubmitActionLabel(label: 'Invite')
                    ->modalWidth(width: MaxWidth::TwoExtraLarge)
                    ->successNotificationTitle(title: 'Invitation sent successfully'),
            ])
            ->heading(heading: 'Collaborators')
            ->recordTitle(title: fn(User $user): string => $user->name.' ('.$user->email.')');
    }
}
