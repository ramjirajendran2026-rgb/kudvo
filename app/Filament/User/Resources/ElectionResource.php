<?php

namespace App\Filament\User\Resources;

use App\Enums\ElectionStatus;
use App\Filament\User\Resources\ElectionResource\Pages;
use App\Filament\User\Resources\ElectionResource\RelationManagers;
use App\Forms\ElectionForm;
use App\Models\Election;
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

    public static function timingForm(Form $form): Form
    {
        return $form
            ->schema(components: [
                ElectionForm::timezoneComponent(),

                ElectionForm::startsAtComponent()
                    ->timezone(fn (Get $get): ?string => $get(path: 'timezone')),

                ElectionForm::endsAtComponent()
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
            'ballot' => Pages\Ballot::route(path: '/{record}/ballot'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems(components: [
            Pages\Dashboard::class,
            Pages\Preference::class,
            Pages\Electors::class,
            Pages\Ballot::class,
        ]);
    }

    public static function getTableCreateAction(): TableCreateAction
    {
        return TableCreateAction::make()
            ->createAnother(condition: false)
            ->modalFooterActionsAlignment(alignment: Alignment::End)
            ->modalWidth(width: MaxWidth::ExtraLarge)
            ->successRedirectUrl(url: fn (Election $election) => static::getUrl(name: 'dashboard', parameters: [$election]));
    }

    public static function getEditAction(): EditAction
    {
        return EditAction::make()
            ->form(form: fn (Form $form): Form => static::form(form: $form))
            ->icon(icon: 'heroicon-m-pencil-square')
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::ExtraLarge);
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
                action: function (Election $nomination, Action $action) {
//                    $nomination->cancel();

                    $action->success();
                }
            )
            ->requiresConfirmation()
            ->color(color: ElectionStatus::CANCELLED->getColor())
            ->icon(icon: ElectionStatus::CANCELLED->getIcon())
            ->label(label: 'Cancel')
            ->modalCancelActionLabel(label: 'No')
            ->modalIcon(icon: ElectionStatus::CANCELLED->getIcon())
            ->modalSubmitActionLabel(label: 'Yes')
            ->successNotificationTitle(title: 'Cancelled');
    }
}
