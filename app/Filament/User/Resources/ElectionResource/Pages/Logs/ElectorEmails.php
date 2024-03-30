<?php

namespace App\Filament\User\Resources\ElectionResource\Pages\Logs;

use App\Enums\MailMessagePurpose;
use App\Filament\User\Resources\ElectionResource\Pages\ElectionPage;
use App\Models\Elector;
use App\Models\Email;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\HasTabs;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ElectorEmails extends ElectionPage implements HasTable
{
    use HasTabs;
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.user.resources.election-resource.pages.logs.elector-emails';

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $activeNavigationIcon = 'heroicon-s-envelope-open';

    protected static ?string $navigationGroup = 'Logs';

    public ?string $activeTab = null;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->loadDefaultActiveTab();
    }

    public static function getNavigationLabel(): string
    {
        return 'Elector Emails';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->getTableQuery())
            ->modifyQueryUsing($this->modifyQueryWithActiveTab(...))
            ->columns([
                Tables\Columns\TextColumn::make('sno')
                    ->label('#')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('notifiable.display_name')
                    ->description(description: fn (Email $email) => $email->notifiable->membership_number)
                    ->label('Elector')
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query
                            ->where('notifiable_type', Elector::class)
                            ->whereExists(function (QueryBuilder $query) use ($search) {
                                $query
                                    ->select('id')
                                    ->from('electors')
                                    ->whereColumn('electors.id', 'emails.notifiable_id')
                                    ->where(
                                        fn (QueryBuilder $query) => $query
                                            ->where('membership_number', "$search")
                                            ->orWhere('full_name', 'like', "%$search%")
                                    );
                            })
                    )
                    ->wrap(),

                Tables\Columns\TextColumn::make('to_address')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime(timezone: $this->getElection()->timezone)
                    ->sortable()
                    ->wrap(),
            ]);
    }

    protected function getTableQuery(): Builder|Relation|null
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
        return [
            MailMessagePurpose::BallotLink->value => Tab::make(label: MailMessagePurpose::BallotLink->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => MailMessagePurpose::BallotLink->getTabQuery($query)),

            MailMessagePurpose::BallotMfaCode->value => Tab::make(label: MailMessagePurpose::BallotMfaCode->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => MailMessagePurpose::BallotMfaCode->getTabQuery($query)),

            MailMessagePurpose::VotedConfirmation->value => Tab::make(label: MailMessagePurpose::VotedConfirmation->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => MailMessagePurpose::VotedConfirmation->getTabQuery($query)),

            MailMessagePurpose::VotedBallotCopy->value => Tab::make(label: MailMessagePurpose::VotedBallotCopy->getLabel())
                ->modifyQueryUsing(callback: fn (Builder $query) => MailMessagePurpose::VotedBallotCopy->getTabQuery($query)),
        ];
    }
}
