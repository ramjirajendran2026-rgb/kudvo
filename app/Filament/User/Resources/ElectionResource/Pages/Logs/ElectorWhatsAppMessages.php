<?php

namespace App\Filament\User\Resources\ElectionResource\Pages\Logs;

use App\Enums\ElectionCollaboratorPermission;
use App\Enums\WhatsAppMessagePurpose;
use App\Enums\WhatsAppMessageStatus;
use App\Filament\Exports\ElectorWhatsAppMessageExporter;
use App\Filament\User\Resources\ElectionResource\Pages\ElectionPage;
use App\Models\Election;
use App\Models\Elector;
use App\Models\WhatsAppMessage;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class ElectorWhatsAppMessages extends ElectionPage implements HasTable
{
    use HasTabs;
    use InteractsWithTable;

    protected static string $view = 'filament-panels::resources.pages.list-records';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $activeNavigationIcon = 'heroicon-s-chat-bubble-left-right';

    public ?string $activeTab = null;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->loadDefaultActiveTab();
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage($election)
            && filled($election->preference)
            && (
                self::hasBallotLink($election)
                || self::hasMfa($election)
                || self::hasVotedConfirmation($election)
                || self::hasVotedBallot($election)
            );
    }

    public static function canAccess(array $parameters = []): bool
    {
        return parent::canAccess($parameters) && static::canAccessPage($parameters['record']);
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.user.election-resource.pages.logs.elector_whats_app_messages.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.user.election-resource.pages.logs.elector_whats_app_messages.navigation_group');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => $this->getTableQuery()
                    ->when(
                        value: ! $this->hasReadAccess(),
                        callback: fn (Builder $query) => $query->whereKey(null)
                    )
            )
            ->modifyQueryUsing($this->modifyQueryWithActiveTab(...))
            ->columns([
                TextColumn::make('sno')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('whatsappable.display_name')
                    ->description(description: fn (WhatsAppMessage $smsMessage) => $smsMessage->whatsappable->membership_number)
                    ->label('Elector')
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query
                            ->whereHasMorph(
                                relation: 'whatsappable',
                                types: [Elector::class],
                                callback: fn (Builder $query) => $query
                                    ->where('membership_number', "$search")
                                    ->orWhere('full_name', 'like', "%$search%")
                            )
                    )
                    ->wrap(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('created_at')
                    ->dateTime(timezone: $this->getElection()->timezone)
                    ->label(label: 'Sent at')
                    ->sortable()
                    ->wrap(),
            ])
            ->defaultSort(column: 'created_at', direction: 'desc')
            ->filters(filters: [
                SelectFilter::make('status')
                    ->options(options: WhatsAppMessageStatus::class)
                    ->label(label: 'Status'),
            ])
            ->headerActions(actions: [
                ExportAction::make()
                    ->columnMapping(condition: false)
                    ->exporter(exporter: ElectorWhatsAppMessageExporter::class)
                    ->fileName(name: fn (self $livewire): string => $livewire->getExportFileName())
                    ->icon(icon: 'heroicon-s-arrow-down-tray')
                    ->label(label: 'Export All')
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalSubmitActionLabel(label: 'Confirm')
                    ->modalHeading(heading: 'Export WhatsApp Message Logs')
                    ->options(options: [
                        'timezone' => $this->getElection()->timezone,
                    ])
                    ->visible(condition: fn (): bool => $this->hasFullAccess()),
            ]);
    }

    protected function getTableQuery(): Builder | Relation | null
    {
        return WhatsAppMessage::query()
            ->whereHasMorph(
                relation: 'whatsappable',
                types: [Elector::class],
                callback: fn (Builder $query) => $query->whereMorphedTo(relation: 'event', model: $this->getElection())
            );
    }

    public function getTabs(): array
    {
        $tabs = [];

        if ($this->hasBallotLink($this->getElection())) {
            $tabs[WhatsAppMessagePurpose::BallotLink->value] = Tab::make(label: WhatsAppMessagePurpose::BallotLink->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => WhatsAppMessagePurpose::BallotLink->getTabQuery($query));
        }

        if ($this->hasMfa($this->getElection())) {
            $tabs[WhatsAppMessagePurpose::BallotMfaCode->value] = Tab::make(label: WhatsAppMessagePurpose::BallotMfaCode->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => WhatsAppMessagePurpose::BallotMfaCode->getTabQuery($query));
        }

        if ($this->hasVotedConfirmation($this->getElection())) {
            $tabs[WhatsAppMessagePurpose::VotedConfirmation->value] = Tab::make(label: WhatsAppMessagePurpose::VotedConfirmation->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => WhatsAppMessagePurpose::VotedConfirmation->getTabQuery($query));
        }

        if ($this->hasVotedBallot($this->getElection())) {
            $tabs[WhatsAppMessagePurpose::VotedBallotCopy->value] = Tab::make(label: WhatsAppMessagePurpose::VotedBallotCopy->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => WhatsAppMessagePurpose::VotedBallotCopy->getTabQuery($query));
        }

        return $tabs;
    }

    protected function getExportFileName(): string
    {
        return Str::kebab($this->activeTab . '-whatsapp-logs-') . $this->getElection()->code;
    }

    public static function hasBallotLink(Election $election): bool
    {
        return $election->preference->ballot_link_whatsapp;
    }

    public static function hasMfa(Election $election): bool
    {
        return $election->preference->mfa_whatsapp;
    }

    public static function hasVotedConfirmation(Election $election): bool
    {
        return $election->preference->voted_confirmation_whatsapp;
    }

    public static function hasVotedBallot(Election $election): bool
    {
        return $election->preference->voted_ballot_whatsapp;
    }

    public function hasReadAccess(): bool
    {
        return $this->isOwner() ||
            $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->elector_logs !== ElectionCollaboratorPermission::NoAccess;
    }

    public function hasFullAccess(): bool
    {
        return $this->isOwner() ||
            $this->getElection()->getCollaboratorPermissions(Filament::auth()->user())->elector_logs === ElectionCollaboratorPermission::FullAccess;
    }
}
