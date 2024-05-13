<?php

namespace App\Filament\User\Resources\ElectionResource\Pages;

use App\Models\ElectionPlan;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use Nnjeim\World\Models\Country;

class Plan extends ElectionPage
{
    protected static string $view = 'filament.user.resources.election-resource.pages.plan';

    public function getSubNavigation(): array
    {
        return [];
    }

    public function getPlans(): Collection
    {
        $currency = Country::firstWhere('iso2', $this->getElection()->organisation->country)
            ?->currency?->code ?? 'USD';

        $plans = ElectionPlan::where('currency', $currency)->oldest('sort')->get();
        if ($plans->isEmpty()) {
            $plans = ElectionPlan::where('currency', 'USD')->oldest('sort')->get();
        }

        return $plans;
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
            ->label(label: 'Select')
            ->successRedirectUrl(url: Dashboard::getUrl(parameters: [$this->getElection()]));
    }
}
