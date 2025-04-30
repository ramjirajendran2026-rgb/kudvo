<?php

namespace App\Filament\User\Resources;

use App\Enums\NominationStatus;
use App\Filament\User\Resources\NominationResource\Pages\Dashboard;
use App\Filament\User\Resources\NominationResource\Pages\Electors;
use App\Filament\User\Resources\NominationResource\Pages\ManageNominations;
use App\Filament\User\Resources\NominationResource\Pages\Nominees;
use App\Filament\User\Resources\NominationResource\Pages\Positions;
use App\Filament\User\Resources\NominationResource\Pages\Preference;
use App\Filament\User\Resources\NominationResource\Widgets\NominationStatsOverview;
use App\Forms\NominationForm;
use App\Models\Nomination;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class NominationResource extends Resource
{
    protected static ?string $model = Nomination::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Election management';

    public static function isShouldRegisterNavigation(): bool
    {
        return config('app.nomination.enabled');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(columns: null)
            ->schema(components: [
                NominationForm::nameComponent(),

                NominationForm::descriptionComponent(),

                NominationForm::selfNominationComponent(),

                NominationForm::nominatorThresholdComponent()
                    ->inlineLabel(),
            ]);
    }

    public static function timingForm(Form $form): Form
    {
        return $form
            ->schema(components: [
                NominationForm::timezoneComponent(),

                NominationForm::startsAtComponent()
                    ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),

                NominationForm::endsAtComponent()
                    ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),
            ]);
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
            ->emptyStateActions(actions: [
                static::getTableCreateAction(),
            ])
            ->emptyStateIcon(icon: static::getNavigationIcon())
            ->headerActions(actions: [
                static::getTableCreateAction(),
            ])
            ->heading(heading: Str::title(value: static::getPluralModelLabel()))
            ->recordUrl(url: fn (Nomination $record) => static::getUrl(name: 'dashboard', parameters: [$record]))
            ->relationship(relationship: fn (): Relation => Filament::getTenant()?->nominations());
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNominations::route(path: '/'),
            'dashboard' => Dashboard::route(path: '{record}'),
            'preference' => Preference::route(path: '{record}/preference'),
            'electors' => Electors::route(path: '{record}/electors'),
            'positions' => Positions::route(path: '{record}/positions'),
            'nominees' => Nominees::route(path: '{record}/nominees'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems(components: [
            Dashboard::class,
            Preference::class,
            Electors::class,
            Positions::class,
            Nominees::class,
        ]);
    }

    public static function getWidgets(): array
    {
        return [
            NominationStatsOverview::class,
        ];
    }

    public static function getTableCreateAction(): TableCreateAction
    {
        return TableCreateAction::make()
            ->createAnother(condition: false)
            ->modalFooterActionsAlignment(alignment: Alignment::End)
            ->successRedirectUrl(url: fn (Nomination $record) => static::getUrl(name: 'dashboard', parameters: [$record]));
    }

    public static function getEditAction(): EditAction
    {
        return EditAction::make()
            ->form(form: fn (Form $form): Form => static::form(form: $form))
            ->icon(icon: 'heroicon-m-pencil-square');
    }

    public static function getEditTimingAction(): EditAction
    {
        return EditAction::make(name: 'editTiming')
            ->form(form: fn (Form $form): Form => static::timingForm(form: $form))
            ->groupedIcon(icon: 'heroicon-m-clock')
            ->icon(icon: 'heroicon-m-clock')
            ->label(label: 'Update Timing')
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium)
            ->mutateRecordDataUsing(callback: function (array $data): array {
                $data['timezone'] ??= Filament::getTenant()?->timezone;
                $data['starts_at'] ??= now(tz: $data['timezone'] ?? null)->addDays()->startOfDay()->addHours(value: 8);
                $data['ends_at'] ??= now(tz: $data['timezone'] ?? null)->addDays()->startOfDay()->addHours(value: 18);

                return $data;
            });
    }

    public static function getCancelAction(): Action
    {
        return Action::make(name: 'cancel')
            ->action(
                action: function (Nomination $nomination, Action $action) {
                    $nomination->cancel();

                    $action->success();
                }
            )
            ->requiresConfirmation()
            ->color(color: NominationStatus::CANCELLED->getColor())
            ->icon(icon: NominationStatus::CANCELLED->getIcon())
            ->label(label: 'Cancel')
            ->modalCancelActionLabel(label: 'No')
            ->modalIcon(icon: NominationStatus::CANCELLED->getIcon())
            ->modalSubmitActionLabel(label: 'Yes')
            ->successNotificationTitle(title: 'Cancelled');
    }
}
