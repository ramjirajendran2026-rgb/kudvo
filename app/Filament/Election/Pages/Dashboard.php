<?php

namespace App\Filament\Election\Pages;

use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Models\Nominee;
use Illuminate\Database\Eloquent\Collection;
use Jenssegers\Agent\Agent;

/**
 * @property Collection<Nominee> $nominees
 */
class Dashboard extends \Filament\Pages\Dashboard
{
    use InteractsWithElection;

    public function mount()
    {
        $agent = app(Agent::class);

        dd(
            $agent->device(),
            $agent->browser(),
            $agent->platform(),
            $agent->isDesktop(),
            $agent->isPhone(),
            $agent->isChrome(),
            $agent->isiOS(),
            $agent->isAndroidOS(),
            $agent->mobileGrade(),
            $agent,
        );
    }
}
