<?php

namespace App\Filament\User\Resources\MeetingResource\Pages\Reports;

use App\Enums\MailMessagePurpose;
use App\Filament\User\Resources\MeetingResource;
use App\Models\Email;
use App\Models\Meeting;
use App\Models\Participant;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ParticipantEmails extends ManageRelatedRecords
{
    protected static string $resource = MeetingResource::class;

    protected static string $relationship = 'participantEmails';

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $activeNavigationIcon = 'heroicon-s-envelope-open';

    public function getBreadcrumb(): string
    {
        return 'Reports';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function getNavigationLabel(): string
    {
        return 'Emails';
    }

    public function getTitle(): string | Htmlable
    {
        return static::getNavigationLabel();
    }

    public function getTabs(): array
    {
        return collect(MailMessagePurpose::cases())
            ->filter(callback: fn (MailMessagePurpose $purpose) => $purpose->getEventType() === Meeting::class)
            ->mapWithKeys(callback: fn (MailMessagePurpose $purpose) => [
                $purpose->value => Tab::make(label: $purpose->getLabel())
                    ->modifyQueryUsing(callback: fn (Builder $query) => $purpose->getTabQuery($query)),
            ])
            ->all();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sno')
                    ->label('#')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('notifiable.name')
                    ->description(description: fn (Email $email) => $email->notifiable?->membership_number)
                    ->label(label: 'Participant')
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query
                            ->whereHasMorph(
                                relation: 'notifiable',
                                types: [Participant::class],
                                callback: fn (Builder $query) => $query
                                    ->where('membership_number', "$search")
                                    ->orWhere('name', 'like', "%$search%")
                            )
                    )
                    ->wrap(),

                Tables\Columns\TextColumn::make('to_address')
                    ->label(label: 'Email Address')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime(timezone: $this->getOwnerRecord()->timezone)
                    ->sortable()
                    ->wrap(),
            ])
            ->defaultSort(column: 'sent_at', direction: 'desc')
            ->hiddenFilterIndicators()
            ->filters(filters: [
                Tables\Filters\Filter::make(name: 'bounced_at')
                    ->default()
                    ->label(label: 'Bounced'),

                Tables\Filters\Filter::make(name: 'complained_at')
                    ->label(label: 'Complained'),

                Tables\Filters\Filter::make(name: 'delivered_at')
                    ->default()
                    ->label(label: 'Delivered'),

                Tables\Filters\Filter::make(name: 'delivery_delayed_at')
                    ->default()
                    ->label(label: 'Delivery Delayed'),

                Tables\Filters\Filter::make(name: 'rejected_at')
                    ->default()
                    ->label(label: 'Rejected'),

                Tables\Filters\Filter::make(name: 'rendering_failed_at')
                    ->default()
                    ->label(label: 'Rendering Failed'),

                Tables\Filters\Filter::make(name: 'sent_at')
                    ->label(label: 'Sent'),
            ], layout: Tables\Enums\FiltersLayout::AboveContent);
    }
}
