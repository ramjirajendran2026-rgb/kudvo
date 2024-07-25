<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Enums\ElectionCollaboratorPermission;
use App\Models\Election;
use App\Models\ElectionMonitorToken;
use Filament\Facades\Filament;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Jenssegers\Agent\Agent;

class MonitorTokens extends ElectionPage implements HasTable
{
    use InteractsWithRelationshipTable;

    protected static string $view = 'filament.user.resources.election-resource.pages.monitor-tokens';

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $activeNavigationIcon = 'heroicon-s-finger-print';

    public static function getRelationshipName(): string
    {
        return 'monitorTokens';
    }

    public function getOwnerRecord(): Election
    {
        return $this->getElection();
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.user.election-resource.pages.monitor_tokens.navigation_label');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort(column: 'id', direction: 'desc')
            ->headerActions(actions: [
                Action::make(name: 'create')
                    ->requiresConfirmation()
                    ->color(color: 'info')
                    ->label(label: __('filament.user.election-resource.pages.monitor_tokens.table.actions.create.label'))
                    ->successNotificationTitle(title: __('filament.user.election-resource.pages.monitor_tokens.table.actions.create.success_notification.title'))
                    ->visible(condition: fn (): bool => $this->hasFullAccess())
                    ->action(action: function (self $livewire, Action $action): void {
                        $livewire->getElection()->monitorTokens()->create();

                        $action->success();
                    }),
            ])
            ->columns(components: [
                TextColumn::make(name: 'key')
                    ->copyable()
                    ->copyableState(state: fn (ElectionMonitorToken $token): string => $token->getLink())
                    ->icon(icon: 'heroicon-m-document-duplicate')
                    ->iconPosition(iconPosition: IconPosition::After)
                    ->label(label: __('filament.user.election-resource.pages.monitor_tokens.table.key.label'))
                    ->size(size: TextColumn\TextColumnSize::ExtraSmall),

                TextColumn::make(name: 'activated_at')
                    ->dateTime(timezone: $this->getElection()->timezone)
                    ->label(label: __('filament.user.election-resource.pages.monitor_tokens.table.activated_at.label')),

                TextColumn::make(name: 'user_agent')
                    ->formatStateUsing(callback: fn (?Agent $state): ?string => filled($state) ? $state->platform() . ' - ' . $state->browser() : null)
                    ->label(label: __('filament.user.election-resource.pages.monitor_tokens.table.user_agent.label')),
            ])
            ->actions(actions: [
                DeleteAction::make()
                    ->iconButton()
                    ->visible(condition: fn (): bool => $this->hasFullAccess()),
            ]);
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage(election: $election) &&
            static::can(action: 'viewAnyMonitorToken', election: $election);
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::canAccessPage(election: $parameters['record']);
    }

    public function hasReadAccess(): bool
    {
        return $this->isOwner() ||
            $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->monitor_tokens !== ElectionCollaboratorPermission::NoAccess;
    }

    public function hasFullAccess(): bool
    {
        return $this->isOwner() ||
            $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->monitor_tokens === ElectionCollaboratorPermission::FullAccess;
    }
}
