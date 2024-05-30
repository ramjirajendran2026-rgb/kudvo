<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Filament\User\Resources\ElectionResource;
use App\Models\ElectionPlan;
use Filament\Actions\Action;
use Filament\Actions\SelectAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Nnjeim\World\Models\Country;

class Plan extends ElectionPage
{
    protected static string $view = 'filament.user.resources.election-resource.pages.plan';

    public string $currency = 'USD';

    public int $totalElectors = 500;

    public ?int $activePlanId = null;

    public static function getNavigationLabel(): string
    {
        return __('filament.user.election-resource.pages.plan.navigation_label');
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->currency = Country::firstWhere('iso2', $this->getElection()->organisation->country)
            ?->currency?->code ?? config('app.default_currency');

        if (! in_array($this->currency, config('app.supported_currencies'))) {
            $this->currency = config('app.default_currency');
        }

        $this->activePlanId = $this->getElection()->plan_id;
        if (filled($this->activePlanId)) {
            $this->currency = ElectionPlan::findOrFail($this->activePlanId)->currency;
        }
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getPlans(): Collection
    {
        $plans = ElectionPlan::where('currency', $this->currency)->oldest('sort')->get();
        if ($plans->isEmpty()) {
            $plans = ElectionPlan::where('currency', 'USD')->oldest('sort')->get();
        }

        return $plans;
    }

    protected function getHeaderActions(): array
    {
        return [
            ElectionResource::getEditAction()
                ->iconButton(),

            SelectAction::make(name: 'currency')
                ->options(Arr::mapWithKeys(config('app.supported_currencies'), fn ($currency) => [$currency => $currency])),
        ];
    }

    public function choosePlanAction()
    {
        return Action::make(name: 'choosePlan')
            ->action(action: function (array $arguments, self $livewire, Action $action) {
                $plan = ElectionPlan::findOrFail($arguments['plan_id']);

                $livewire->getElection()->update(attributes: ['plan_id' => $plan->getKey()]);

                $action->success();
            })
            ->extraAttributes(attributes: [
                'class' => 'w-full',
            ])
            ->label(label: __('filament.user.election-resource.pages.plan.actions.choose_plan.label'))
            ->successRedirectUrl(url: Dashboard::getUrl(parameters: [$this->getElection()]));
    }
}
