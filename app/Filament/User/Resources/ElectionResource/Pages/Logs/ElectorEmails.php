<?php

namespace App\Filament\User\Resources\ElectionResource\Pages\Logs;

use App\Enums\ElectionCollaboratorPermission;
use App\Enums\EmailTableFilter;
use App\Enums\MailMessagePurpose;
use App\Filament\Exports\ElectorEmailExporter;
use App\Filament\User\Resources\ElectionResource\Pages\ElectionPage;
use App\Models\Election;
use App\Models\Elector;
use App\Models\Email;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class ElectorEmails extends ElectionPage implements HasTable
{
    use HasTabs;
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament-panels::resources.pages.list-records';

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $activeNavigationIcon = 'heroicon-s-envelope-open';

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
        return __('filament.user.election-resource.pages.logs.elector_emails.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.user.election-resource.pages.logs.elector_emails.navigation_group');
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
                Tables\Columns\TextColumn::make('sno')
                    ->label('#')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('notifiable.display_name')
                    ->description(description: fn (Email $email) => $email->notifiable?->membership_number)
                    ->label('Elector')
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query
                            ->whereHasMorph(
                                relation: 'notifiable',
                                types: [Elector::class],
                                callback: fn (Builder $query) => $query
                                    ->where('membership_number', "$search")
                                    ->orWhere('full_name', 'like', "%$search%")
                            )
                    )
                    ->wrap(),

                Tables\Columns\TextColumn::make('to_address')
                    ->label(label: 'Email Address')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime(timezone: $this->getElection()->timezone)
                    ->sortable()
                    ->wrap(),
            ])
            ->defaultSort(column: 'sent_at', direction: 'desc')
            ->hiddenFilterIndicators()
            ->filters(filters: EmailTableFilter::getFilters(), layout: Tables\Enums\FiltersLayout::AboveContent)
            ->headerActions(actions: [
                Tables\Actions\ExportAction::make()
                    ->columnMapping(condition: false)
                    ->exporter(exporter: ElectorEmailExporter::class)
                    ->fileName(name: fn (self $livewire): string => $livewire->getExportFileName())
                    ->icon(icon: 'heroicon-s-arrow-down-tray')
                    ->label(label: 'Export All')
                    ->modalFooterActionsAlignment(alignment: Alignment::Center)
                    ->modalSubmitActionLabel(label: 'Confirm')
                    ->modalHeading(heading: 'Export Email Logs')
                    ->options(options: [
                        'timezone' => $this->getElection()->timezone,
                    ])
                    ->visible(condition: fn (): bool => $this->hasFullAccess()),
            ]);
    }

    protected function getTableQuery(): Builder | Relation | null
    {
        return Email::query()
            ->whereHasMorph(
                relation: 'notifiable',
                types: [Elector::class],
                callback: fn (Builder $query) => $query->whereMorphedTo(relation: 'event', model: $this->getElection())
            );
    }

    public function getTabs(): array
    {
        $tabs = [];

        if ($this->hasBallotLink($this->getElection())) {
            $tabs[MailMessagePurpose::MeetingInvitation->value] = Tab::make(label: MailMessagePurpose::MeetingInvitation->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => MailMessagePurpose::MeetingInvitation->getTabQuery($query));
        }

        if ($this->hasMfa($this->getElection())) {
            $tabs[MailMessagePurpose::BallotMfaCode->value] = Tab::make(label: MailMessagePurpose::BallotMfaCode->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => MailMessagePurpose::BallotMfaCode->getTabQuery($query));
        }

        if ($this->hasVotedConfirmation($this->getElection())) {
            $tabs[MailMessagePurpose::VotedConfirmation->value] = Tab::make(label: MailMessagePurpose::VotedConfirmation->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => MailMessagePurpose::VotedConfirmation->getTabQuery($query));
        }

        if ($this->hasVotedBallot($this->getElection())) {
            $tabs[MailMessagePurpose::VotedBallotCopy->value] = Tab::make(label: MailMessagePurpose::VotedBallotCopy->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => MailMessagePurpose::VotedBallotCopy->getTabQuery($query));
        }

        return $tabs;
    }

    protected function getExportFileName(): string
    {
        return Str::kebab($this->activeTab . '-email-logs-') . $this->getElection()->code;
    }

    public static function hasBallotLink(Election $election): bool
    {
        return $election->preference->ballot_link_mail;
    }

    public static function hasMfa(Election $election): bool
    {
        return $election->preference->mfa_mail;
    }

    public static function hasVotedConfirmation(Election $election): bool
    {
        return $election->preference->voted_confirmation_mail;
    }

    public static function hasVotedBallot(Election $election): bool
    {
        return $election->preference->voted_ballot_mail;
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
