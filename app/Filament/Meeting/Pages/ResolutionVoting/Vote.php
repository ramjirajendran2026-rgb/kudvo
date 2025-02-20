<?php

namespace App\Filament\Meeting\Pages\ResolutionVoting;

use App\Filament\Meeting\Pages\Index;
use Livewire\Attributes\On;

class Vote extends BasePage
{
    #[On('meeting-resolution-response-submitted')]
    public function redirectToHome(): void
    {
        $this->redirect(Index::getUrl());
    }
}
