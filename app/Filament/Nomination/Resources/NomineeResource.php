<?php

namespace App\Filament\Nomination\Resources;

use App\Enums\NomineeScrutinyStatus;
use App\Facades\Kudvo;
use App\Filament\Base\Contracts\HasElector;
use App\Filament\Base\Contracts\HasNomination;
use App\Filament\Nomination\Resources\NomineeResource\Pages;
use App\Forms\NomineeForm;
use App\Models\Nominator;
use App\Models\Nominee;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class NomineeResource extends Resource
{
    protected static ?string $model = Nominee::class;

    protected static ?string $modelLabel = 'Nomination';

    protected static ?string $slug = '/';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(components: []);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->actions(actions: [
                static::getAcceptAction(),
            ])
            ->emptyStateActions(actions: [
                static::getCreateAction(),
            ])
            ->headerActions(actions: [
                static::getCreateAction()
                    ->visible(condition: fn (Tables\Contracts\HasTable $livewire): bool => $livewire->getAllTableRecordsCount()),
            ])
            ->heading(heading: static::getPluralModelLabel())
            ->paginated(condition: false)
            ->query(
                fn() => Nominee::query()
                    ->whereHas(
                        'position',
                        fn(Builder $query) => $query->whereMorphedTo('event', Kudvo::getNomination())
                    )
                    ->where(
                        fn(Builder $query) => $query
                            ->whereBelongsTo(auth()->user(), 'elector')
                            ->orWhereHas(
                                'proposer',
                                fn(Builder $query) => $query->where('elector_id', auth()->id())
                            )
                            ->orWhereHas(
                                'nominators',
                                fn(Builder $query) => $query->where('elector_id', auth()->id())
                            )
                    )
            )
            ->columns([
                Tables\Columns\TextColumn::make(name: '#')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make(name: 'position.name')
                    ->color(color: 'primary')
                    ->size(size: TextColumnSize::Large)
                    ->weight(weight: FontWeight::Bold),

                Tables\Columns\TextColumn::make(name: 'membership_number')
                    ->description(description: fn (Nominee $nominee): ?string => $nominee->full_name)
                    ->icon(icon: fn (Nominee $nominee): ?string => $nominee->status->getIcon())
                    ->iconColor(color: fn (Nominee $nominee): ?string => $nominee->status->getColor())
                    ->label(label: 'Nominee'),

                Tables\Columns\TextColumn::make(name: 'proposer')
                    ->description(description: fn (?Nominator $state): ?string => $state?->full_name)
                    ->formatStateUsing(callback: fn (?Nominator $state): string => $state?->membership_number)
                    ->icon(icon: fn (?Nominator $state): ?string => $state?->status->getIcon())
                    ->iconColor(color: fn (?Nominator $state): ?string => $state?->status->getColor()),

                Tables\Columns\TextColumn::make(name: 'seconders')
                    ->formatStateUsing(callback: fn (Nominator $state): string => $state->display_name)
                    ->icon(icon: fn (Nominator $state): ?string => $state->status->getIcon())
                    ->iconColor(color: fn (Nominator $state): ?string => $state->status->getColor())
                    ->listWithLineBreaks()
                    ->size(size: TextColumnSize::Small)
                    ->wrap(),

                Tables\Columns\TextColumn::make(name: 'scrutiny_status')
                    ->badge()
                    ->color(color: fn (NomineeScrutinyStatus $state): ?string => $state->getColor())
                    ->icon(icon: fn (NomineeScrutinyStatus $state): ?string => $state->getIcon())
                    ->label(label: 'Scrutiny'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNominees::route(path: '/'),
            'create' => Pages\CreateNominee::route(path: '/nominate'),
        ];
    }

    public static function getUrl(string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        $parameters['nomination'] ??= Kudvo::getNomination();

        return parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant);
    }

    public static function getCreateAction(): Tables\Actions\CreateAction
    {
        return Tables\Actions\CreateAction::make();
    }

    public static function getAcceptAction()
    {
        return Tables\Actions\Action::make(name: 'accept')
            ->requiresConfirmation()
            ->authorize(abilities: 'accept')
            ->button()
            ->failureNotificationTitle(title: 'Failed')
            ->modalDescription(description: fn (Nominee $nominee): HtmlString => static::getAcceptanceDescription(nominee: $nominee))
            ->modalHeading(heading: 'Confirmation')
            ->successNotificationTitle(title: 'Accepted')
            ->form(
                form: fn (Nominee $nominee, HasElector | HasNomination $livewire): ?array => $nominee->elector
                    ?->is($livewire->getElector()) ?
                    [
                        NomineeForm::photoComponent()
                            ->required()
                            ->visible(condition: (bool) $livewire->getNomination()->preference->candidate_photo),

                        NomineeForm::bioComponent()
                            ->required()
                            ->visible(condition: (bool) $livewire->getNomination()->preference->candidate_bio),

                        NomineeForm::attachmentComponent()
                            ->required()
                            ->visible(condition: (bool) $livewire->getNomination()->preference->candidate_attachment),
                    ] :
                    null
            )
            ->action(action: function (Tables\Actions\Action $action, Nominee $nominee, HasElector $livewire) {
                $elector = $livewire->getElector();

                if ($nominee->elector->is(model: $elector)) {
                    $nominee->accept();

                    if ($nominee->isAccepted()) {
                        $action->success();
                    } else {
                        $action->failure();
                    }

                    return;
                }

                $nominator = $nominee->nominators()
                    ->whereBelongsTo(related: $elector)
                    ->first();

                if (blank(value: $nominator)) {
                    $action->failure();

                    return;
                }

                $nominator->accept();

                if ($nominator->isAccepted()) {
                    $action->success();
                } else {
                    $action->failure();
                }
            });
    }

    public static function getAcceptanceDescription(Nominee $nominee): HtmlString
    {
        return new HtmlString(
            html: $nominee->proposer?->display_name .
            ' nominating ' .
            "<b>$nominee->display_name</b>" .
            ' as ' .
            "<b>{$nominee->position->name}</b>" .
            ' for the upcoming ' .
            "<b>{$nominee->position->event->name}</b>" .
            '.'
        );
    }
}
