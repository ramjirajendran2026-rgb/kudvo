<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Filament\User\Contracts\HasElection;
use App\Filament\User\Contracts\HasElectorGroups;
use App\Filament\User\Contracts\HasNomination;
use App\Filament\User\Resources\ElectionResource;
use App\Filament\User\Resources\NominationResource;
use App\Filament\User\Resources\NominationResource\Pages\Dashboard;
use App\Models\Election;
use App\Models\Nomination;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property Form $form
 */
class ElectionPage extends Page implements HasElectorGroups, HasElection
{
    protected static string $resource = ElectionResource::class;

    #[Locked]
    public Election $election;

    #[Locked]
    public array $electorGroups = [];

    public function mount(int|string $record): void
    {
        $this->election = $this->resolveElection(key: $record);

        $this->authorizeAccess();

        $this->electorGroups = $this->getElection()->getElectorGroups();
    }

    public function mountCanAuthorizeAccess(): void
    {
    }

    protected function resolveElection(int | string $key): Election
    {
        /** @var Election $nomination */
        $nomination = app(ElectionResource::getModel())
            ->resolveRouteBindingQuery(ElectionResource::getEloquentQuery(), $key, ElectionResource::getRecordRouteKeyName())
            ->withCount(relations: ['positions', 'electors'])
            ->first();

        abort_if(boolean: blank(value: $nomination), code: Response::HTTP_NOT_FOUND);

        return $nomination;
    }

    public function getElection(): Election
    {
        return $this->election;
    }

    public function getElectorGroups(): array
    {
        return $this->electorGroups;
    }

    public function getTitle(): string|Htmlable
    {
        return static::getNavigationLabel().' - '.$this->getRecordTitle();
    }

    public function getRecordTitle(): string | Htmlable
    {
        if (! ElectionResource::hasRecordTitle()) {
            return ElectionResource::getTitleCaseModelLabel();
        }

        return ElectionResource::getRecordTitle($this->getElection());
    }

    public function getHeading(): string|Htmlable
    {
        return $this->getRecordTitle();
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (! $this->getElection()->isTimingConfigured()) {
            return null;
        }

        return new HtmlString(
            html: <<<HTML
<b>{$this->getElection()->starts_at_local->format(format: 'M d, Y h:i A (T)')}</b> to
<b>{$this->getElection()->ends_at_local->format(format: 'M d, Y h:i A (T)')}</b>
HTML
        );
    }

    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getElection(),
        ];
    }

    public function getSubNavigation(): array
    {
        return ElectionResource::getRecordSubNavigation($this);
    }

    public function getWidgetData(): array
    {
        return [
            'record' => $this->getElection(),
            'nomination' => $this->getElection(),
        ];
    }

    protected function getMountedActionFormModel(): Model | string | null
    {
        return $this->getElection();
    }

    public function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        if (! static::canAccessPage(election: $this->election)) {
            $this->redirect(Dashboard::getUrl(parameters: [$this->election]));
        }
    }

    protected function configureAction(Action $action): void
    {
        $action
            ->record($this->getElection())
            ->recordTitle($this->getRecordTitle());
    }

    protected function getHeaderActions(): array
    {
        return [
            ElectionResource::getEditAction()
                ->iconButton(),

            ActionGroup::make(actions: [
                ElectionResource::getEditTimingAction()
                    ->modalHeading(heading: fn (self $livewire) => $livewire->getRecordTitle()),

                ElectionResource::getCancelAction()

            ])->dropdownPlacement(placement: 'bottom-end'),
        ];
    }

    public static function can(string $action, Election $election): bool
    {
        return ElectionResource::can(action: $action, record: $election);
    }

    public static function cannot(string $action, Election $election): bool
    {
        return ! static::can(action: $action, election: $election);
    }

    public static function canAccessPage(Election $election): bool
    {
        return ElectionResource::canView(record: $election);
    }
}
