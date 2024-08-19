<?php

namespace App\Filament\Nomination\Pages;

use App\Filament\Nomination\Pages\Concerns\InteractsWithNomination;
use App\Models\Nominee;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;

/**
 * @property Collection<Nominee> $nominees
 */
class Dashboard extends \Filament\Pages\Dashboard
{
    use InteractsWithNomination;

    //    protected static string $view = 'filament.nomination.pages.dashboard';

    protected static bool $isDiscovered = false;

    #[Computed(persist: true)]
    public function nominees(): Collection
    {
        return Nominee::query()
            ->whereBelongsTo(related: $this->getElector())
            ->when(
                value: $this->getNomination()->nominator_threshold,
                callback: fn (Builder $query): Builder => $query
                    ->orWhereHas(
                        relation: 'nominators',
                        callback: fn (Builder $query): Builder => $query->whereBelongsTo(related: $this->getElector())
                    )
            )
            ->get();
    }

    protected function getNominateAction()
    {
        return Action::make(name: 'nominate')
            ->model(model: Nominee::class)
            ->form(form: [
                Select::make(name: 'position_id')
                    ->relationship(name: 'position'),
            ]);
    }
}
