<?php

namespace App\Filament\Election\Pages\Auth;

use App\Facades\Kudvo;
use App\Models\Election;
use App\Models\Elector;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BasePage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class Login extends BasePage
{
    public function getHeading(): string|Htmlable
    {
        return Kudvo::getElection()->name;
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        $elector = Kudvo::getElection()->electors()
            ->firstWhere('membership_number', $data['membership_number']);

        if (blank(value: $elector)) {
            $this->throwFailureValidationException();
        }
        Filament::auth()->login(user: $elector);

        /** @var Elector $elector */
        $elector = Filament::auth()->user();

        if (! $elector->canAccessPanel(Filament::getCurrentPanel())) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        $elector->createAuthSession(
            sessionId: session()->getId(),
            guardName: Filament::getAuthGuard(),
            request: request(),
        );

        return app(LoginResponse::class);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'membership_number' => $data['membership_number'],
            'event_type' => Election::class,
            'event_id' => Kudvo::getElection()?->getKey(),
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.membership_number' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                $this->getMembershipNumberComponent(),
            ]);
    }

    protected function getMembershipNumberComponent()
    {
        return TextInput::make(name: 'membership_number')
            ->label(label: 'Your membership number')
            ->required();
    }
}
