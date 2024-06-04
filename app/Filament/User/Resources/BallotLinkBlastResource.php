<?php

namespace App\Filament\User\Resources;

use App\Enums\BallotLinkBlastStatus;
use App\Filament\Base\Contracts\HasElection;
use App\Models\BallotLinkBlast;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TableDeleteAction;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BallotLinkBlastResource extends Resource
{
    protected static ?string $model = BallotLinkBlast::class;

    protected static bool $isDiscovered = false;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getModelLabel(): string
    {
        return __('filament.user.ballot-link-blast-resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.user.ballot-link-blast-resource.plural_model_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                DateTimePicker::make(name: 'scheduled_at')
                    ->helperText(text: __('filament.user.ballot-link-blast-resource.form.scheduled_at.helper_text'))
                    ->hiddenLabel()
                    ->label(label: __('filament.user.ballot-link-blast-resource.form.scheduled_at.label'))
                    ->minDate(date: fn (HasElection $livewire): string => now($livewire->getElection()->timezone)->format('Y-m-d H:i'))
                    ->required()
                    ->seconds(condition: false)
                    ->timezone(timezone: fn (HasElection $livewire): ?string => $livewire->getElection()->timezone),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->actions(actions: [
                static::getTableEditAction(),
                static::getTableDeleteAction(),
            ])
            ->columns(components: [
                TextColumn::make(name: '#')
                    ->rowIndex(),

                TextColumn::make(name: 'scheduled_at')
                    ->dateTime(format: 'M j, Y h:i A')
                    ->timezone(timezone: fn (HasElection $livewire): ?string => $livewire->getElection()->timezone),

                TextColumn::make(name: 'status')
                    ->badge(),
            ])
            ->headerActions(actions: [
                static::getTableCreateAction(),
            ])
            ->modelLabel(label: static::getModelLabel())
            ->pluralModelLabel(label: static::getPluralModelLabel())
            ->recordTitleAttribute(attribute: static::getRecordTitleAttribute());
    }

    public static function getTableCreateAction(): TableCreateAction
    {
        return TableCreateAction::make()
            ->createAnother(condition: false)
            ->form(form: fn (Form $form): Form => static::form(form: $form))
            ->icon(icon: 'heroicon-m-plus')
            ->model(model: static::getModel())
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalHeading(heading: 'Schedule a blast')
            ->modelLabel(label: 'Blast')
            ->modalWidth(width: MaxWidth::Medium)
            ->visible(condition: fn (HasElection $livewire): bool => $livewire->getElection()->is_published);
    }

    public static function getTableEditAction(): TableEditAction
    {
        return TableEditAction::make()
            ->form(form: fn (Form $form): Form => static::form($form))
            ->iconButton()
            ->modalCancelAction(action: false)
            ->modalFooterActionsAlignment(alignment: Alignment::Center)
            ->modalWidth(width: MaxWidth::Medium)
            ->visible(condition: fn (BallotLinkBlast $record): bool => $record->status === BallotLinkBlastStatus::Scheduled);
    }

    public static function getTableDeleteAction(): TableDeleteAction
    {
        return TableDeleteAction::make()
            ->iconButton()
            ->visible(condition: fn (BallotLinkBlast $record): bool => $record->status === BallotLinkBlastStatus::Scheduled);
    }
}
