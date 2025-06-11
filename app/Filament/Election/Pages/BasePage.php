<?php

namespace App\Filament\Election\Pages;

use App\Events\ElectorRevokedFromBoothEvent;
use App\Facades\Kudvo;
use App\Filament\Base\Contracts\HasElection;
use App\Filament\Base\Contracts\HasElector;
use App\Filament\Election\ElectionPanel;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Filament\Election\Pages\Concerns\InteractsWithElector;
use App\Models\CandidateGroup;
use App\Models\Election;
use App\Models\Position;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Filament\Pages\Page;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

use function Filament\authorize;

/**
 * @property Form $form
 * @property array<int, string> $candidateGroups
 * @property Collection<int, Position> $positions
 */
abstract class BasePage extends Page implements HasElection, HasElector
{
    use InteractsWithElection;
    use InteractsWithElector;

    protected static bool $shouldRegisterNavigation = false;

    public bool $mock;

    public static function can(string $action)
    {
        try {
            return authorize(action: $action, model: Kudvo::getElection() ?? Election::class)->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }

    public function getListeners(): array
    {
        $listeners = parent::getListeners();

        if (Kudvo::isBoothDevice()) {
            $listeners['echo:election-booth.' . Kudvo::getElectionBoothToken()?->getKey() . ',.' . ElectorRevokedFromBoothEvent::getBroadcastName()] = 'destroySession';
        }

        return $listeners;
    }

    public function mount(Request $request): void
    {
        $this->mock = $request->query(key: 'mock', default: false);
    }

    public function getPanel(): ElectionPanel
    {
        /** @var ElectionPanel $panel */
        $panel = Filament::getCurrentPanel();

        return $panel;
    }

    public function isSpa(): bool
    {
        return Filament::getCurrentPanel()->hasSpaMode();
    }

    public function getRedirectUrl(): ?string
    {
        return Index::getUrl(parameters: $this->isMock() ? ['mock' => 1] : []);
    }

    public function isMock(): bool
    {
        return $this->mock;
    }

    #[On(event: 'session-expired')]
    public function destroySession()
    {
        Filament::auth()->logout();

        $this->skipRender();

        return app(LogoutResponse::class);
    }

    #[Computed(persist: true)]
    public function positions(): Collection
    {
        $electorSegmentIds = $this->getElection()->preference->segmented_ballot ?
            $this->getElector()->segments()->pluck('id') :
            [];

        return Position::whereMorphedTo('event', $this->getElection())
            ->oldest('sort')
            ->get()
            ->when(
                $this->getElection()->preference->waterfall_voting,
                fn ($query) => $query->where('voting_starts_at', '<=', now())
                    ->where(
                        fn ($query) => $query->whereNull('voting_ends_at')
                            ->orWhere('voting_ends_at', '>=', now()),
                    ),
            )
            ->when(
                value: $this->getElection()->preference->segmented_ballot,
                callback: fn (Collection $query) => $query
                    ->where(
                        fn (Position $position) => $position->segments()
                            ->whereIn('id', $electorSegmentIds)
                            ->exists(),
                    )
            )
            ->when(
                $this->getElection()->preference->waterfall_voting &&
                ! $this->getElection()->preference->voted_ballot_update &&
                filled($this->getBallot()?->position_keys),
                fn (Collection $collection) => $collection->reject(fn (Position $position) => collect($this->getBallot()->position_keys)->contains($position->uuid))
            );
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
}
