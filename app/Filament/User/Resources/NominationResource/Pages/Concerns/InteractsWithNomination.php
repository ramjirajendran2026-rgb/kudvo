<?php

namespace App\Filament\User\Resources\NominationResource\Pages\Concerns;

use App\Filament\User\Resources\NominationResource;
use App\Models\Nomination;
use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;
use Symfony\Component\HttpFoundation\Response;

trait InteractsWithNomination
{
    #[Locked]
    public Nomination $nomination;

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

    public function getBreadcrumbs(): array
    {
        return [];
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

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
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

    protected function configureAction(Action $action): void
    {
        $action
            ->record($this->getNomination())
            ->recordTitle($this->getRecordTitle());
    }
}
