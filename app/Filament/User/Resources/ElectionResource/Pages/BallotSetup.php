<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Filament\Contracts\HasElection;
use App\Filament\User\Resources\CandidateResource;
use App\Filament\User\Resources\PositionResource;
use App\Forms\CandidateForm;
use App\Forms\PositionForm;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Str;

class BallotSetup extends ElectionPage
{
    protected static string $view = 'filament.user.resources.election-resource.pages.ballot.setup';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $activeNavigationIcon = 'heroicon-s-list-bullet';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(components: [
                RepeatableEntry::make(name: 'positions')
                    ->contained(condition: false)
                    ->hiddenLabel()
                    ->schema(components: [
                        Section::make(heading: fn (Position $state): ?string => $state->name)
                            ->compact()
                            ->description(description: fn (Position $state): ?string => $state->quota.Str::plural(value: ' Post', count: $state->quota))
                            ->headerActions(actions: [
                                $this->getCreateCandidateAction(),

                                $this->getEditPositionAction(),

                                $this->getDeletePositionAction(),
                            ])
                            ->schema(components: [
                                RepeatableEntry::make(name: 'candidates')
                                    ->contained(condition: false)
                                    ->hiddenLabel()
                                    ->schema(components: [
                                        Split::make(schema: [
                                            SpatieMediaLibraryImageEntry::make(name: 'photo')
                                                ->circular()
                                                ->collection(collection: Candidate::MEDIA_COLLECTION_PHOTO)
                                                ->defaultImageUrl(url: fn (Candidate $record): ?string => filament()->getUserAvatarUrl($record))
                                                ->grow(condition: false)
                                                ->hiddenLabel()
                                                ->size(size: 80),

                                            Group::make()
                                                ->schema(components: [
                                                    TextEntry::make(name: 'full_name')
                                                        ->suffixActions(actions: [
                                                            $this->getEditCandidateAction(),

                                                            $this->getDeleteCandidateAction(),
                                                        ])
                                                        ->hiddenLabel()
                                                        ->size(size: TextEntry\TextEntrySize::Large),

                                                    Split::make(schema: [
                                                        TextEntry::make(name: 'membership_number')
                                                            ->grow(condition: false)
                                                            ->hiddenLabel(),

                                                        TextEntry::make(name: 'email')
                                                            ->grow(condition: false)
                                                            ->hiddenLabel(),

                                                        TextEntry::make(name: 'phone')
                                                            ->grow(condition: false)
                                                            ->hiddenLabel(),
                                                    ])
                                                ]),
                                        ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected function makeInfolist(): Infolist
    {
        return parent::makeInfolist()
            ->record($this->getElection());
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getCreatePositionAction(),

            ...parent::getHeaderActions(),
        ];
    }

    protected function getCreatePositionAction(): CreateAction
    {
        return CreateAction::make(name: 'createPosition')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'createPosition',
                    election: $livewire->getElection()
                )
            )
            ->form(form: fn (Form $form): Form => PositionResource::form(form: $form))
            ->modalWidth(width: MaxWidth::ExtraLarge)
            ->model(model: Position::class)
            ->record(record: null)
            ->relationship(relationship: fn(HasElection $livewire) => $livewire->getElection()->positions());
    }

    protected function getEditPositionAction(): Action
    {
        return Action::make(name: 'editPosition')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'updateAnyPosition',
                    election: $livewire->getElection()
                )
            )
            ->action(action: function (Position $record, array $data): void {
                $record->fill(attributes: $data);

                $record->save();
            })
            ->fillForm(data: fn (Position $record): array => $record->attributesToArray())
            ->form(form: fn (Form $form): Form => PositionResource::form(form: $form))
            ->icon(icon: 'heroicon-m-pencil-square')
            ->iconButton()
            ->modalHeading(heading: fn (Position $record): string => "Edit $record->name")
            ->modalSubmitActionLabel(label: 'Save changes')
            ->modalWidth(width: MaxWidth::ExtraLarge);
    }

    protected function getDeletePositionAction(): Action
    {
        return Action::make(name: 'deletePosition')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'deleteAnyPosition',
                    election: $livewire->getElection()
                )
            )
            ->requiresConfirmation()
            ->action(action: function (Position $record): void {
                $record->delete();
            })
            ->color(color: 'danger')
            ->icon(icon: 'heroicon-m-trash')
            ->iconButton()
            ->modalHeading(heading: fn (Position $record): string => "Delete $record->name");
    }

    protected function getCreateCandidateAction(): Action
    {
        return Action::make(name: 'createCandidate')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'createCandidate',
                    election: $livewire->getElection()
                )
            )
            ->action(action: function (Position $record, array $data, Form $form): void {
                $candidate = new Candidate();
                $candidate->fill(attributes: $data);
                $record->candidates()->save(model: $candidate);

                $form->model($candidate)->saveRelationships();
            })
            ->form(form: fn (Form $form) => CandidateResource::form(form: $form))
            ->icon(icon: 'heroicon-m-plus')
            ->label(label: 'New candidate')
            ->outlined()
            ->size(size: ActionSize::Small);
    }

    protected function getCreateCandidatesAction(): Action
    {
        return Action::make(name: 'createCandidates')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'createCandidates',
                    election: $livewire->getElection()
                )
            )
            ->action(action: function (Position $record, array $data): void {
                foreach ($data['candidates'] as $candidate) {
                    $record->candidates()->create(attributes: $candidate);
                }
            })
            ->form(form: [
                Repeater::make(name: 'candidates')
                    ->defaultItems(1)
                    ->schema(components: CandidateResource::getFormComponents()),
            ])
            ->icon(icon: 'heroicon-m-plus')
            ->iconButton();
    }

    protected function getEditCandidateAction(): Action
    {
        return Action::make(name: 'editCandidate')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'updateAnyCandidate',
                    election: $livewire->getElection()
                )
            )
            ->action(action: function (Candidate $record, array $data): void {
                $record->fill(attributes: $data);

                $record->save();
            })
            ->fillForm(data: fn (Candidate $record): array => $record->attributesToArray())
            ->form(
                form: fn (Form $form, Candidate $record): Form => CandidateResource::form(form: $form)
                    ->model(model: $record)
            )
            ->icon(icon: 'heroicon-m-pencil-square')
            ->iconButton()
            ->modalFooterActionsAlignment(alignment: Alignment::End)
            ->modalHeading(heading: fn (Candidate $record): string => "Edit $record->membership_number")
            ->modalSubmitActionLabel(label: 'Save changes');
    }

    protected function getDeleteCandidateAction(): Action
    {
        return Action::make(name: 'deleteCandidate')
            ->authorize(
                abilities: fn (HasElection $livewire): bool => static::can(
                    action: 'deleteAnyCandidate',
                    election: $livewire->getElection()
                )
            )
            ->requiresConfirmation()
            ->action(action: function (Candidate $record): void {
                $record->delete();
            })
            ->color(color: 'danger')
            ->icon(icon: 'heroicon-m-trash')
            ->iconButton()
            ->modalHeading(heading: fn (Candidate $record): string => "Delete $record->membership_number");
    }

    public static function canAccessPage(Election $election): bool
    {
        return parent::canAccessPage(election: $election) &&
            static::can(action: 'viewBallotSetup', election: $election);
    }
}
