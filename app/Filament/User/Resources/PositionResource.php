<?php

namespace App\Filament\User\Resources;

use App\Filament\Base\Contracts\HasElection;
use App\Filament\Base\Contracts\HasElectorGroups;
use App\Forms\PositionForm;
use App\Models\Position;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Arr;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static bool $isDiscovered = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(components: static::getFormComponents());
    }

    public static function getFormComponents(): array
    {
        return [
            PositionForm::nameComponent(),

            PositionForm::quotaComponent()
                ->inlineLabel()
                ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->voting_method->canHavePositionQuota()),

            PositionForm::abstainComponent()
                ->live()
                ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->voting_method->canHavePositionQuota()),

            PositionForm::thresholdComponent()
                ->inlineLabel()
                ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->voting_method->canHavePositionQuota()),

            PositionForm::segmentsComponent()
                ->visible(condition: fn ($livewire) => $livewire instanceof HasElection && $livewire->getElection()->preference?->segmented_ballot),

            PositionForm::groupsComponent()
                ->options(
                    options: fn (HasElectorGroups $livewire): array => Arr::mapWithKeys(
                        array: $livewire->getElectorGroups(),
                        callback: fn (string $item): array => [$item => $item]
                    )
                )
                ->required()
                ->visible(
                    condition: fn (HasElectorGroups $livewire): bool => filled(value: $livewire->getElectorGroups())
                ),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(components: [
                TextColumn::make(name: 'name')
                    ->label(label: __('filament.user.position-resource.table.name.label'))
                    ->wrap(),

                TextColumn::make(name: 'quota')
                    ->alignCenter()
                    ->label(label: __('filament.user.position-resource.table.quota.label'))
                    ->numeric(),

                TextColumn::make(name: 'threshold')
                    ->alignCenter()
                    ->label(label: __('filament.user.position-resource.table.threshold.label'))
                    ->numeric(),

                TextColumn::make(name: 'segments.name')
                    ->badge()
                    ->label(label: __('filament.user.position-resource.table.segments.label'))
                    ->visible(condition: fn ($livewire) => $livewire instanceof HasElection && $livewire->getElection()->preference?->segmented_ballot)
                    ->wrap(),
            ])
            ->defaultSort(column: 'sort')
            ->reorderable(column: 'sort');
    }

    public static function getTableCreateAction(): TableCreateAction
    {
        return TableCreateAction::make()
            ->createAnother(condition: false)
            ->form(form: fn (Form $form): Form => static::form($form))
            ->icon(icon: 'heroicon-m-plus')
            ->model(model: static::getModel())
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modelLabel(label: static::getModelLabel())
            ->modalWidth(width: MaxWidth::Medium);
    }

    public static function getTableEditAction(): TableEditAction
    {
        return TableEditAction::make()
            ->form(form: fn (Form $form): Form => static::form($form))
            ->iconButton()
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium)
            ->mutateFormDataUsing(callback: function (array $data): array {
                $data['threshold'] = $data['abstain'] ? $data['threshold'] : $data['quota'];

                return $data;
            });
    }

    public static function getTableDeleteAction(): TableDeleteAction
    {
        return TableDeleteAction::make()
            ->iconButton();
    }
}
