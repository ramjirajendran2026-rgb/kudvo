<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Filament\User\Resources\ElectionResource;
use App\Models\Election;
use App\Models\ElectionMonitorToken;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Concerns\InteractsWithRelationshipTable;
use Filament\Resources\Pages\Page;
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

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort(column: 'id', direction: 'desc')
            ->headerActions(actions: [
                Action::make(name: 'createNewToken')
                    ->requiresConfirmation()
                    ->color(color: 'info')
                    ->action(action: function (self $livewire): void {
                        $livewire->getElection()->monitorTokens()->create();

                        Notification::make()
                            ->title(title: 'Created')
                            ->success()
                            ->send();
                    }),
            ])
            ->columns(components: [
                TextColumn::make(name: 'key')
                    ->copyable()
                    ->icon(icon: 'heroicon-m-document-duplicate')
                    ->iconPosition(iconPosition: IconPosition::After)
                    ->size(size: TextColumn\TextColumnSize::ExtraSmall),

                TextColumn::make(name: 'activated_at')
                    ->dateTime(timezone: $this->getElection()->timezone),

                TextColumn::make(name: 'user_agent')
                    ->formatStateUsing(callback: fn (?Agent $state): ?string => filled($state) ? $state->platform().' - '.$state->browser() : null)
                    ->label(label: 'Device'),
            ])
            ->actions(actions: [
                DeleteAction::make()
                    ->iconButton(),
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
}
