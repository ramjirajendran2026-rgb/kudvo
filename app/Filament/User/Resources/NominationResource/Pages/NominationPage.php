<?php

namespace App\Filament\User\Resources\NominationResource\Pages;

use App\Filament\Contracts\HasElectorGroups;
use App\Filament\Contracts\HasNomination;
use App\Filament\User\Resources\NominationResource;
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
abstract class NominationPage extends Page implements HasElectorGroups, HasNomination
{
    protected static string $resource = NominationResource::class;

    #[Locked]
    public Nomination $nomination;

    #[Locked]
    public array $electorGroups = [];

    public function mount(int|string $record): void
    {
        $this->nomination = $this->resolveNomination(key: $record);

        $this->authorizeAccess();

        $this->electorGroups = $this->getNomination()->getElectorGroups();
    }

    public function mountCanAuthorizeAccess(): void
    {
    }

    protected function resolveNomination(int | string $key): Nomination
    {
        /** @var Nomination $nomination */
        $nomination = app(NominationResource::getModel())
            ->resolveRouteBindingQuery(NominationResource::getEloquentQuery(), $key, NominationResource::getRecordRouteKeyName())
            ->withCount(relations: ['positions', 'electors'])
            ->first();

        abort_if(boolean: blank(value: $nomination), code: Response::HTTP_NOT_FOUND);

        return $nomination;
    }

    public function getNomination(): Nomination
    {
        return $this->nomination;
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
        if (! NominationResource::hasRecordTitle()) {
            return NominationResource::getTitleCaseModelLabel();
        }

        return NominationResource::getRecordTitle($this->getNomination());
    }

    public function getHeading(): string|Htmlable
    {
        return $this->getRecordTitle();
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (! $this->getNomination()->isTimingConfigured()) {
            return null;
        }

        return new HtmlString(
            html: <<<HTML
<b>{$this->getNomination()->starts_at_local->format(format: 'M d, Y h:i A (T)')}</b> to
<b>{$this->getNomination()->ends_at_local->format(format: 'M d, Y h:i A (T)')}</b>
HTML
        );
    }

    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getNomination(),
        ];
    }

    public function getSubNavigation(): array
    {
        return NominationResource::getRecordSubNavigation($this);
    }

    public function getWidgetData(): array
    {
        return [
            'record' => $this->getNomination(),
            'nomination' => $this->getNomination(),
        ];
    }

    protected function getMountedActionFormModel(): Model | string | null
    {
        return $this->getNomination();
    }

    public function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        if (! static::canAccessPage(nomination: $this->nomination)) {
            $this->redirect(Dashboard::getUrl(parameters: [$this->nomination]));
        }
    }

    protected function configureAction(Action $action): void
    {
        $action
            ->record($this->getNomination())
            ->recordTitle($this->getRecordTitle());
    }

    protected function getHeaderActions(): array
    {
        return [
            NominationResource::getEditAction()
                ->iconButton()
                ->visible(condition: $this->canEditNomination()),

            ActionGroup::make(actions: [
                NominationResource::getEditTimingAction()
                    ->modalHeading(heading: fn (self $livewire) => $livewire->getRecordTitle())
                    ->visible(condition: $this->canEditTiming()),

                NominationResource::getCancelAction()
                    ->visible(condition: $this->canCancelNomination()),

            ])->dropdownPlacement(placement: 'bottom-end'),
        ];
    }

    public static function can(string $action, Nomination $nomination): bool
    {
        return NominationResource::can(action: $action, record: $nomination);
    }

    public static function cannot(string $action, Nomination $nomination): bool
    {
        return ! static::can(action: $action, nomination: $nomination);
    }

    public static function canAccessPage(Nomination $nomination): bool
    {
        return NominationResource::canView(record: $nomination);
    }

    protected function canEditTiming(): bool
    {
        return static::can(action: 'updateTiming', nomination: $this->nomination);
    }

    protected function canCancelNomination(): bool
    {
        return static::can(action: 'cancel', nomination: $this->nomination);
    }

    protected function canEditNomination(): bool
    {
        return static::can(action: 'update', nomination: $this->nomination);
    }
}
