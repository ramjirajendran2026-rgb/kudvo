<?php

namespace App\Filament\Meeting\Pages\ResolutionVoting;

use App\Filament\Meeting\Http\Middleware\IdentifyPanelState;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Livewire\Attributes\On;

class Preview extends BasePage
{
    protected static string | array $withoutRouteMiddleware = [Authenticate::class, IdentifyPanelState::class];

    protected static string | array $routeMiddleware = ['signed'];

    protected bool $preview = true;

    #[On('meeting-resolution-response-submitted')]
    public function redirectToHome(): void
    {
        $this->redirect(Filament::getCurrentPanel()->getUrl());
    }
}
