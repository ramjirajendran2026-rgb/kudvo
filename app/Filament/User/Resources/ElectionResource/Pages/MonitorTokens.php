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

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';

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

                TextColumn::make(name: 'device')
                    ->getStateUsing(callback: fn (ElectionMonitorToken $record): ?Agent => blank($record->user_agent) ? null : new Agent(userAgent: $record->user_agent))
                    ->formatStateUsing(callback: fn (?Agent $state): ?string => blank($state) ? null : ($state->device().'/'.$state->browser())),
            ])
            ->actions(actions: [
                DeleteAction::make()
                    ->iconButton(),
            ]);
    }
}
