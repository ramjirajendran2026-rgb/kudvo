<?php

namespace App\Filament\User\Resources;

use App\Enums\ElectionStatus;
use App\Filament\Base\Contracts\HasElection;
use App\Filament\User\Resources\ElectionResource\Pages;
use App\Filament\User\Resources\ElectionResource\RelationManagers;
use App\Filament\User\Resources\ElectionResource\Widgets\ElectionStatsOverview;
use App\Filament\User\Resources\ElectionResource\Widgets\VotedBallots;
use App\Forms\ElectionForm;
use App\Models\Election;
use App\Models\Elector;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction as CreateTableAction;
use Filament\Tables\Actions\DeleteAction as DeleteTableAction;
use Filament\Tables\Actions\ReplicateAction as ReplicateTableAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class ElectionResource extends Resource
{
    protected static ?string $model = Election::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $activeNavigationIcon = 'heroicon-s-archive-box';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(columns: null)
            ->schema([
                ElectionForm::nameComponent(),
            ]);
    }

    public static function editFormSchema(): array
    {
        return [
            ElectionForm::nameComponent(),
        ];
    }

    public static function timingForm(Form $form): Form
    {
        return $form
            ->schema(components: static::timingFormSchema());
    }

    public static function timingFormSchema(): array
    {
        return [
            ElectionForm::timezoneComponent(),

            ElectionForm::startsAtComponent()
                ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),

            ElectionForm::endsAtComponent()
                ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),

            ElectionForm::boothStartsAtComponent()
                ->timezone(fn (Get $get): ?string => $get(path: 'timezone'))
                ->visible(condition: fn (?Election $record): bool => $record?->isBoothVotingEnabled()),

            ElectionForm::boothEndsAtComponent()
                ->timezone(fn (Get $get): ?string => $get(path: 'timezone'))
                ->visible(condition: fn (?Election $record): bool => $record?->isBoothVotingEnabled()),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(name: 'code')
                    ->badge()
                    ->copyable()
                    ->icon(icon: 'heroicon-m-clipboard-document')
                    ->iconPosition(iconPosition: IconPosition::After)
                    ->label(label: 'Code')
                    ->searchable(),

                Tables\Columns\TextColumn::make(name: 'name')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make(name: 'status')
                    ->alignCenter()
                    ->badge(),
            ])
            ->actions(actions: [
                Tables\Actions\ActionGroup::make(actions: [
                    static::getReplicateAction(),

                    DeleteTableAction::make(),
                ]),
            ])
            ->emptyStateActions(actions: [
                static::getCreateTableAction(),
            ])
            ->emptyStateIcon(icon: static::getNavigationIcon())
            ->headerActions(actions: [
                static::getCreateTableAction(),
            ])
            ->heading(heading: Str::title(value: static::getPluralModelLabel()))
            ->recordUrl(url: fn (Election $election) => static::getUrl(name: 'dashboard', parameters: [$election]))
            ->relationship(relationship: fn (): Relation => Filament::getTenant()?->elections());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageElections::route(path: '/'),
            'dashboard' => Pages\Dashboard::route(path: '/{record}'),
            'preference' => Pages\Preference::route(path: '/{record}/preference'),
            'electors' => Pages\Electors::route(path: '/{record}/electors'),
            'ballot.setup' => Pages\BallotSetup::route(path: '/{record}/ballot/setup'),
            'result' => Pages\Result::route(path: '/{record}/result'),
            'monitor_tokens' => Pages\MonitorTokens::route(path: '/{record}/monitor-tokens'),
            'booth_tokens' => Pages\BoothTokens::route(path: '/{record}/booth-tokens'),

            'logs.elector_emails' => Pages\Logs\ElectorEmails::route(path: '/{record}/logs/elector-emails'),
            'logs.elector_sms_messages' => Pages\Logs\ElectorSmsMessages::route(path: '/{record}/logs/elector-sms-messages'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems(components: [
            Pages\Dashboard::class,
            Pages\Preference::class,
            Pages\Electors::class,
            Pages\BallotSetup::class,
            Pages\Result::class,
            Pages\MonitorTokens::class,
            Pages\BoothTokens::class,
            Pages\Logs\ElectorEmails::class,
            Pages\Logs\ElectorSmsMessages::class,
        ]);
    }

    public static function getWidgets(): array
    {
        return [
            ElectionStatsOverview::class,
            VotedBallots::class,
        ];
    }

    public static function getCreateTableAction(): CreateTableAction
    {
        return CreateTableAction::make()
            ->createAnother(condition: false)
            ->modalFooterActionsAlignment(alignment: Alignment::End)
            ->modalWidth(width: MaxWidth::ExtraLarge)
            ->successRedirectUrl(url: fn (Election $election) => static::getUrl(name: 'dashboard', parameters: [$election]));
    }

    public static function getEditAction(): EditAction
    {
        return EditAction::make()
            ->authorize(abilities: 'update')
            ->form(form: static::editFormSchema())
            ->icon(icon: 'heroicon-m-pencil-square')
            ->label(label: 'Edit title')
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::ExtraLarge);
    }

    public static function getSetTimingAction(): EditAction
    {
        return EditAction::make(name: 'setTiming')
            ->authorize(abilities: 'setTiming')
            ->form(form: static::timingFormSchema())
            ->groupedIcon(icon: 'heroicon-m-clock')
            ->icon(icon: 'heroicon-m-clock')
            ->label(label: 'Set Timing')
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalHeading(heading: fn (HasElection $livewire): ?string => $livewire->getElection()->name)
            ->modalWidth(width: MaxWidth::Medium)
            ->mutateRecordDataUsing(callback: function (array $data, HasElection $livewire): array {
                $data['timezone'] ??= Filament::getTenant()?->timezone;
                $data['starts_at'] ??= now(tz: $data['timezone'] ?? null)->addDays()->startOfDay()->addHours(value: 8);
                $data['ends_at'] ??= now(tz: $data['timezone'] ?? null)->addDays()->startOfDay()->addHours(value: 18);

                if ($livewire->getElection()->isBoothVotingEnabled()) {
                    $data['booth_starts_at'] ??= now(tz: $data['timezone'] ?? null)->addDays(value: 2)->startOfDay()->addHours(value: 8);
                    $data['booth_ends_at'] ??= now(tz: $data['timezone'] ?? null)->addDays(value: 2)->startOfDay()->addHours(value: 18);
                }

                return $data;
            })
            ->record(record: fn (HasElection $livewire): Election => $livewire->getElection());
    }

    public static function getEditTimingAction(): EditAction
    {
        return static::getSetTimingAction()
            ->authorize(abilities: 'updateTiming')
            ->label(label: 'Edit Timing')
            ->name(name: 'editTiming');
    }

    public static function getCancelAction(): Action
    {
        return Action::make(name: 'cancel')
            ->action(
                action: function (Election $election, Action $action) {
                    $election->cancel();

                    $action->success();
                }
            )
            ->authorize(abilities: 'cancel')
            ->requiresConfirmation()
            ->color(color: ElectionStatus::CANCELLED->getColor())
            ->icon(icon: ElectionStatus::CANCELLED->getIcon())
            ->label(label: 'Cancel')
            ->modalCancelActionLabel(label: 'No')
            ->modalIcon(icon: ElectionStatus::CANCELLED->getIcon())
            ->modalSubmitActionLabel(label: 'Yes')
            ->successNotificationTitle(title: 'Cancelled');
    }

    public static function getPublishAction(): Action
    {
        return Action::make(name: 'publish')
            ->requiresConfirmation()
            ->action(action: function (HasElection $livewire, Action $action, array $data): void {
                $livewire->getElection()->publish();

                if (isset($data['notify_electors']) && $data['notify_electors']) {
                    $livewire->getElection()->electors()
                        ->chunkById(
                            count: 300,
                            callback: fn (Collection $collection) => $collection
                                ->each(
                                    callback: fn (Elector $elector) => $elector
                                        ->sendBallotLink(election: $livewire->getElection())
                                )
                        );
                }

                $action->success();
            })
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'publish',
                    record: $livewire->getElection()
                )
            )
            ->color(color: ElectionStatus::PUBLISHED->getColor())
            ->form(form: [
                Toggle::make(name: 'notify_electors')
                    ->label(label: 'Send ballot link all electors'),
            ])
            ->modalIcon(icon: ElectionStatus::PUBLISHED->getIcon())
            ->successNotificationTitle(title: 'Published');
    }

    public static function getCloseAction(): Action
    {
        return Action::make(name: 'close')
            ->action(action: function (HasElection $livewire, Action $action) {
                $livewire->getElection()->close();

                $action->success();

                $livewire->redirect(url: static::getUrl(name: 'dashboard', parameters: [$livewire->getElection()]));
            })
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'close',
                    record: $livewire->getElection()
                )
            )
            ->requiresConfirmation()
            ->color(color: ElectionStatus::CLOSED->getColor())
            ->icon(icon: ElectionStatus::CLOSED->getIcon())
            ->label(label: fn (HasElection $livewire): string => $livewire->getElection()->is_open ? 'Pre-close' : 'Close')
            ->modalIcon(icon: ElectionStatus::CLOSED->getIcon())
            ->successNotificationTitle(title: 'Closed');
    }

    public static function getGenerateResultAction(): Action
    {
        return Action::make(name: 'generateResult')
            ->requiresConfirmation()
            ->action(action: function (HasElection $livewire, Action $action): void {
                $livewire->getElection()->generateResult();

                $action->success();
            })
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'generateResult',
                    record: $livewire->getElection()
                )
            )
            ->color(color: 'success')
            ->icon(icon: 'heroicon-o-chart-pie')
            ->label(label: 'Generate Result')
            ->successNotificationTitle(title: 'Result generated');
    }

    public static function getReplicateAction(): ReplicateTableAction
    {
        return ReplicateTableAction::make()
            ->after(callback: function (Election $replica, Election $record, array $data): void {
                if ($data['replicate_electors'] ?? false) {
                    $replica->replicateElectors(from: $record);
                }

                if ($data['replicate_ballot_setup'] ?? false) {
                    $replica->replicateBallotSetup(from: $record);
                }
            })
            ->excludeAttributes(attributes: [
                'cancelled_at',
                'code',
                'completed_at',
                'closed_at',
                'published_at',
                'short_code',
            ])
            ->form(form: [
                ElectionForm::nameComponent(),

                Toggle::make(name: 'replicate_electors')
                    ->default(state: true)
                    ->label(label: 'Include electors'),

                Toggle::make(name: 'replicate_ballot_setup')
                    ->default(state: true)
                    ->label(label: 'Include ballot setup'),
            ]);
    }
}
