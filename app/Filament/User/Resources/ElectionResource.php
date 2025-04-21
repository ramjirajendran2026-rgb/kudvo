<?php

namespace App\Filament\User\Resources;

use App\Enums\ElectionStatus;
use App\Filament\Base\Contracts\HasElection;
use App\Filament\User\Resources\ElectionResource\Pages;
use App\Filament\User\Resources\ElectionResource\Widgets\CandidateVotesChart;
use App\Filament\User\Resources\ElectionResource\Widgets\ElectionStatsOverview;
use App\Filament\User\Resources\ElectionResource\Widgets\ElectorDataImportProgress;
use App\Filament\User\Resources\ElectionResource\Widgets\VotedBallots;
use App\Forms\Components\TimezonePicker;
use App\Forms\ElectionForm;
use App\Models\Election;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction as CreateTableAction;
use Filament\Tables\Actions\DeleteAction as DeleteTableAction;
use Filament\Tables\Actions\ReplicateAction as ReplicateTableAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class ElectionResource extends Resource
{
    use Translatable;

    protected static ?string $model = Election::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $activeNavigationIcon = 'heroicon-s-archive-box';

    public static function getModelLabel(): string
    {
        return __(key: 'filament.user.election-resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __(key: 'filament.user.election-resource.plural_model_label');
    }

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

            Fieldset::make(label: 'Online Voting')
                ->schema(components: [
                    ElectionForm::startsAtComponent()
                        ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),

                    ElectionForm::endsAtComponent()
                        ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),
                ]),

            Fieldset::make(label: 'Booth Voting')
                ->visible(condition: fn (?Election $record): bool => $record?->isBoothVotingEnabled())
                ->schema(components: [
                    ElectionForm::boothStartsAtComponent()
                        ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),

                    ElectionForm::boothEndsAtComponent()
                        ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make(schema: [
                    Tables\Columns\Layout\Split::make(schema: [
                        Tables\Columns\TextColumn::make(name: 'code')
                            ->badge()
                            ->size(size: Tables\Columns\TextColumn\TextColumnSize::Large)
                            ->copyable()
                            ->grow(condition: false)
                            ->icon(icon: 'heroicon-m-clipboard-document')
                            ->iconPosition(iconPosition: IconPosition::After)
                            ->label(label: __('filament.user.election-resource.table.code.label')),

                        Tables\Columns\TextColumn::make(name: 'status')
                            ->badge()
                            ->grow(condition: false)
                            ->label(label: __('filament.user.election-resource.table.status.label')),
                    ]),

                    Tables\Columns\TextColumn::make(name: 'name')
                        ->weight(weight: FontWeight::SemiBold)
                        ->label(label: __('filament.user.election-resource.table.name.label'))
                        ->size(size: Tables\Columns\TextColumn\TextColumnSize::Large)
                        ->wrap(),

                    Tables\Columns\Layout\Split::make(schema: [
                        Tables\Columns\TextColumn::make(name: 'starts_at_local')
                            ->dateTime()
                            ->description(description: __('filament.user.election-resource.table.starts_at.label'), position: 'above')
                            ->label(label: __('filament.user.election-resource.table.starts_at.label'))
                            ->tooltip(tooltip: fn (Election $record): ?string => TimezonePicker::getDisplayLabel($record->ends_at_local?->timezone->getName() ?? 'UTC')),

                        Tables\Columns\TextColumn::make(name: 'ends_at_local')
                            ->alignEnd()
                            ->dateTime()
                            ->description(description: __('filament.user.election-resource.table.ends_at.label'), position: 'above')
                            ->label(label: __('filament.user.election-resource.table.ends_at.label'))
                            ->tooltip(tooltip: fn (Election $record): ?string => TimezonePicker::getDisplayLabel($record->starts_at_local?->timezone->getName() ?? 'UTC')),
                    ]),
                ])->space(),
            ])
            ->actions(actions: [
                Tables\Actions\ActionGroup::make(actions: [
                    static::getReplicateAction(),

                    DeleteTableAction::make(),
                ])->dropdownPlacement(placement: 'bottom-start'),
            ])
            ->contentGrid(grid: [
                'md' => 2,
            ])
            ->defaultSort(column: 'id', direction: 'desc')
            ->emptyStateActions(actions: [
                static::getCreateTableAction(),
            ])
            ->emptyStateIcon(icon: static::getNavigationIcon())
            ->headerActions(actions: [
                static::getCreateTableAction(),
            ])
            ->paginationPageOptions(options: [6, 10, 20])
            ->recordClasses(classes: fn (Election $election) => match ($election->status->getColor()) {
                'info' => '!bg-info-100 dark:!bg-info-400/30',
                'primary' => '!bg-primary-100 dark:!bg-primary-400/30',
                'success' => '!bg-success-100 dark:!bg-success-400/30',
                'warning' => '!bg-warning-100 dark:!bg-warning-400/30',
                default => null,
            })
            ->recordUrl(url: fn (Election $election) => $election->getPendingStep()?->getUrl([$election]) ?? static::getUrl(name: 'dashboard', parameters: [$election]))
            ->relationship(relationship: fn (): Relation => Filament::getTenant()?->elections());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageElections::route(path: '/'),
            'dashboard' => Pages\Dashboard::route(path: '/{record}'),
            'plan' => Pages\Plan::route(path: '/{record}/plan'),
            'preference' => Pages\Preference::route(path: '/{record}/preference'),
            'electors' => Pages\Electors::route(path: '/{record}/electors'),
            'ballot.setup' => Pages\BallotSetup::route(path: '/{record}/ballot/setup'),
            'ballot_link_blasts' => Pages\BallotLinkBlasts::route(path: '/{record}/ballot-link-blasts'),
            'result' => Pages\Result::route(path: '/{record}/result'),
            'monitor_tokens' => Pages\MonitorTokens::route(path: '/{record}/monitor-tokens'),
            'booth_tokens' => Pages\BoothTokens::route(path: '/{record}/booth-tokens'),

            'logs.elector_ballots' => Pages\Logs\ElectorBallots::route(path: '/{record}/logs/elector-ballots'),
            'logs.elector_emails' => Pages\Logs\ElectorEmails::route(path: '/{record}/logs/elector-emails'),
            'logs.elector_sms_messages' => Pages\Logs\ElectorSmsMessages::route(path: '/{record}/logs/elector-sms-messages'),
            'logs.elector_whats_app_messages' => Pages\Logs\ElectorWhatsAppMessages::route('/{record}/logs/elector-whats-app-messages'),

            'collaborators' => Pages\Collaborators::route(path: '/{record}/collaborators'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems(components: [
            Pages\Dashboard::class,
            Pages\Preference::class,
            Pages\Electors::class,
            Pages\BallotSetup::class,
            Pages\BallotLinkBlasts::class,
            Pages\Result::class,
            Pages\MonitorTokens::class,
            Pages\BoothTokens::class,
            Pages\Logs\ElectorBallots::class,
            Pages\Logs\ElectorEmails::class,
            Pages\Logs\ElectorSmsMessages::class,
            Pages\Logs\ElectorWhatsAppMessages::class,
            Pages\Collaborators::class,
        ]);
    }

    public static function getWidgets(): array
    {
        return [
            ElectionStatsOverview::class,
            VotedBallots::class,
            ElectorDataImportProgress::class,
            CandidateVotesChart::class,
        ];
    }

    public static function getCreateTableAction(): CreateTableAction
    {
        return CreateTableAction::make()
            ->createAnother(condition: false)
            ->mutateFormDataUsing(callback: function (array $data): array {
                return array_merge($data, ['owner_id' => Filament::auth()->id()]);
            })
            ->successRedirectUrl(url: fn (Election $election) => $election->getPendingStep()?->getUrl([$election]) ?? static::getUrl(name: 'dashboard', parameters: [$election]));
    }

    public static function getEditAction(): EditAction
    {
        return EditAction::make()
            ->authorize(abilities: 'update')
            ->form(form: static::editFormSchema())
            ->icon(icon: 'heroicon-m-pencil-square')
            ->label(label: __('filament.user.election-resource.actions.edit.label'))
            ->modalCancelAction(action: false);
    }

    public static function getSetTimingAction(): EditAction
    {
        return EditAction::make(name: 'setTiming')
            ->authorize(abilities: 'setTiming')
            ->form(form: static::timingFormSchema())
            ->groupedIcon(icon: 'heroicon-m-clock')
            ->icon(icon: 'heroicon-m-clock')
            ->label(label: __('filament.user.election-resource.actions.set_timing.label'))
            ->modalCancelAction(action: false)
            ->modalHeading(heading: fn (HasElection $livewire): ?string => $livewire->getElection()->name)
            ->modalWidth(width: MaxWidth::ExtraLarge)
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
            ->label(label: __('filament.user.election-resource.actions.edit_timing.label'))
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
            ->label(label: __('filament.user.election-resource.actions.cancel.label'))
            ->modalCancelActionLabel(label: __('filament.user.election-resource.actions.cancel.modal_actions.cancel.label'))
            ->modalIcon(icon: ElectionStatus::CANCELLED->getIcon())
            ->modalSubmitActionLabel(label: __('filament.user.election-resource.actions.cancel.modal_actions.submit.label'))
            ->successNotificationTitle(title: __('filament.user.election-resource.actions.cancel.success_notification.title'));
    }

    public static function getPublishAction(): Action
    {
        return Action::make(name: 'publish')
            ->requiresConfirmation()
            ->action(action: function (HasElection $livewire, Action $action, array $data): void {
                $livewire->getElection()->publish();

                if ($livewire->getElection()->preference->isBallotLinkBlastNeeded()) {
                    $livewire->getElection()
                        ->ballotLinkBlasts()
                        ->create(attributes: [
                            'scheduled_at' => $data['scheduled_at'] ?? now(),
                        ]);
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
                ToggleButtons::make(name: 'notify_electors')
                    ->colors(['warning', 'warning'])
                    ->default(state: true)
                    ->helperText(text: 'Delivery may take several minutes, depending on the number of electors. Please plan accordingly.')
                    ->inline()
                    ->label(label: 'Send ballot links to voters')
                    ->live()
                    ->options(options: [
                        true => 'Immediately',
                        false => 'Later',
                    ])
                    ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->preference->isBallotLinkBlastNeeded()),

                DateTimePicker::make(name: 'scheduled_at')
                    ->hiddenLabel()
                    ->label(label: 'Schedule at')
                    ->minDate(date: fn (HasElection $livewire): string => now($livewire->getElection()->timezone)->format('Y-m-d H:i'))
                    ->required()
                    ->seconds(condition: false)
                    ->timezone(timezone: fn (HasElection $livewire): ?string => $livewire->getElection()->timezone)
                    ->visible(condition: fn (Get $get): bool => $get('notify_electors') == false),
            ])
            ->label(label: __('filament.user.election-resource.actions.publish.label'))
            ->modalIcon(icon: ElectionStatus::PUBLISHED->getIcon())
            ->successNotificationTitle(title: __('filament.user.election-resource.actions.publish.success_notification.title'));
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
            ->label(
                label: fn (HasElection $livewire): string => $livewire->getElection()->is_open
                    ? __('filament.user.election-resource.actions.close.pre_close.label')
                    : __('filament.user.election-resource.actions.close.label')
            )
            ->modalIcon(icon: ElectionStatus::CLOSED->getIcon())
            ->successNotificationTitle(title: __('filament.user.election-resource.actions.close.success_notification.title'));
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
            ->label(label: __('filament.user.election-resource.actions.generate_result.label'))
            ->successNotificationTitle(title: __('filament.user.election-resource.actions.generate_result.success_notification.title'));
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
            ->beforeReplicaSaved(callback: function (Election $replica, array $data) {
                $replica->fill($data);
            })
            ->excludeAttributes(attributes: [
                'cancelled_at',
                'code',
                'completed_at',
                'closed_at',
                'published_at',
                'short_code',
                'paid_at',
                'invoice_status',
                'stripe_invoice_id',
                'stripe_invoice_data',
            ])
            ->form(form: [
                ElectionForm::nameComponent(),

                Toggle::make(name: 'replicate_electors')
                    ->default(state: true)
                    ->label(label: __('filament.user.election-resource.actions.replicate.form.replicate_electors.label')),

                Toggle::make(name: 'replicate_ballot_setup')
                    ->default(state: true)
                    ->label(label: __('filament.user.election-resource.actions.replicate.form.replicate_ballot_setup.label')),
            ])
            ->mutateRecordDataUsing(function (HasActions $livewire, Model $record, array $data): array {
                if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                    $data = $translatableContentDriver->getRecordAttributesToArray($record);
                }

                return $data;
            });
    }
}
