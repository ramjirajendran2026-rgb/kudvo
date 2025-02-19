<?php

namespace App\Filament\Meeting\Pages\Auth;

use App\Filament\Meeting\Pages\Concerns\BelongsToMeeting;
use App\Models\Participant;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BasePage;
use Filament\Panel;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;

class Login extends BasePage
{
    use BelongsToMeeting;

    public static function doLogin(Participant $participant, Panel $panel, Request $request): void
    {
        $panel->auth()->login(user: $participant);
        session()->regenerate();

        $participant->createAuthSession(
            sessionId: session()->getId(),
            guardName: $panel->getAuthGuard(),
            request: $request,
        );
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->disabled()
            ->schema([]);
    }

    public function getTitle(): string | Htmlable
    {
        return $this->getMeeting()->name;
    }

    public function getHeading(): string | Htmlable
    {
        return $this->getTitle();
    }

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return MaxWidth::ScreenMedium;
    }

    protected function getLayoutData(): array
    {
        return [
            ...parent::getLayoutData(),

            'maxWidth' => $this->getMaxContentWidth(),
        ];
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->hidden();
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
