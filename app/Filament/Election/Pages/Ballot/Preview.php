<?php

namespace App\Filament\Election\Pages\Ballot;

use App\Filament\Base\Contracts\HasElection;
use App\Filament\Election\ElectionPanel;
use App\Filament\Election\Http\Middleware\EnsureStateIsAllowed;
use App\Filament\Election\Http\Middleware\IdentifyBoothToken;
use App\Filament\Election\Http\Middleware\IdentifyPanelState;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Forms\Components\VotesPicker;
use App\Models\CandidateGroup;
use App\Models\Position;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property array<int, string> $candidateGroups
 * @property EloquentCollection<int, Position> $positions
 */
class Preview extends Page implements HasElection
{
    use InteractsWithElection;

    protected static string $view = 'filament.election.pages.ballot.index';

    protected static ?string $slug = 'ballot/preview';

    public array $data = [];

    public bool $preview = false;

    public bool $flashVotes = false;

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string | Htmlable
    {
        return 'Ballot Preview - ' . $this->getElection()->name;
    }

    public static function getWithoutRouteMiddleware(Panel $panel): string | array
    {
        return [
            IdentifyBoothToken::class,
            IdentifyPanelState::class,
            EnsureStateIsAllowed::class,
            ...Filament::getPanel(id: 'election')->getAuthMiddleware(),
        ];
    }

    public function mountCanAuthorizeAccess(): void
    {
        $this->getElection()->loadCount(relations: 'positions');

        abort_unless(
            boolean: $this->getElection()->positions_count > 0 &&
                $this->getElection()->positions()
                    ->whereHas(
                        relation: 'candidates',
                        count: DB::raw(value: 'positions.quota')
                    )
                    ->count() == $this->getElection()->positions_count,
            code: Response::HTTP_NOT_FOUND,
        );

        $this->form->fill([]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(condition: fn (self $livewire): bool => $this->preview)
            ->model(model: $this->getElection())
            ->operation(operation: $this->preview ? 'preview' : 'create')
            ->statePath(path: 'data')
            ->schema(components: [
                Actions::make(actions: [
                    $this->getBackAction(),
                ]),

                ...$this->positions
                    ->map(
                        callback: fn (Position $position) => VotesPicker::forPosition(
                            uuid: $position->uuid,
                            preference: $this->getElection()->preference,
                        )
                            ->when(
                                $this->getElection()->preference->candidate_group,
                                callback: fn (VotesPicker $picker) => $picker
                                    ->groups($this->candidateGroups)
                            ),
                    )
                    ->toArray(),

                Actions::make(actions: [
                    $this->getBackAction(),

                    Actions\Action::make(name: 'continue')
                        ->label(label: 'Continue')
                        ->action(action: 'submit')
                        ->hidden(condition: fn (self $livewire): bool => $livewire->flashVotes || $livewire->preview)
                        ->size(size: ActionSize::ExtraLarge)
                        ->submit(form: 'submit'),

                    Actions\Action::make(name: 'confirm')
                        ->requiresConfirmation()
                        ->action(action: 'submit')
                        ->label(label: 'Confirm')
                        ->hidden(condition: fn (self $livewire): bool => $livewire->flashVotes || ! $livewire->preview)
                        ->size(size: ActionSize::ExtraLarge),
                ])
                    ->alignment(alignment: fn (self $livewire): Alignment => $livewire->preview ? Alignment::Between : Alignment::End),
            ]);
    }

    #[Computed(persist: true)]
    public function positions(): Collection
    {
        return Position::whereMorphedTo('event', $this->getElection())
            ->oldest('sort')
            ->get();
    }

    #[Computed(persist: true)]
    public function candidateGroups(): array
    {
        return CandidateGroup::query()
            ->whereBelongsTo(related: $this->getElection())
            ->pluck(column: 'short_name', key: 'id')
            ->put(key: 'independent', value: 'Independent')
            ->prepend(value: 'All', key: 'all')
            ->toArray();
    }

    protected function getBackAction()
    {
        return Actions\Action::make(name: 'Back')
            ->action(action: function (self $livewire) {
                $livewire->preview = false;

                $livewire->dispatch(event: 'scroll-to-top');
            })
            ->color(color: 'gray')
            ->hidden(condition: fn (self $livewire): bool => $livewire->flashVotes)
            ->icon(icon: 'heroicon-s-chevron-left')
            ->size(size: ActionSize::ExtraLarge)
            ->visible(condition: fn (self $livewire): bool => $livewire->preview);
    }

    public function submit(): void
    {
        $this->form->getState();

        if (! $this->preview) {
            $this->preview = true;

            Notification::make()
                ->title(title: 'Confirmation')
                ->body(body: 'Please review your selection and confirm')
                ->info()
                ->send();

            $this->dispatch(event: 'scroll-to-top');

            return;
        }

        Notification::make()
            ->title(title: 'Completed')
            ->body(body: 'You have successfully completed the ballot preview')
            ->success()
            ->seconds(seconds: 30)
            ->send();

        $this->redirect(url: self::getUrl());
    }

    public function getPanel(): ElectionPanel
    {
        /** @var ElectionPanel $panel */
        $panel = Filament::getCurrentPanel();

        return $panel;
    }
}
