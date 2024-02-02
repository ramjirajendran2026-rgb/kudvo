<?php

namespace App\Filament\Election\Pages\Auth;

use App\Facades\Kudvo;
use App\Filament\Election\Http\Middleware\EnsureDeviceIsAllowed;
use App\Models\Election;
use App\Models\Elector;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BasePage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

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
            ->firstWhere('phone', $data['phone']);

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
            'phone' => $data['phone'],
            'event_type' => Election::class,
            'event_id' => Kudvo::getElection()?->getKey(),
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.phone' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                $this->getPhoneComponent()
                    ->initialCountry(value: Kudvo::getOrganisation()?->country),
            ]);
    }

    protected function getPhoneComponent()
    {
        return PhoneInput::make(name: 'phone')
            ->label(label: 'Your phone number')
            ->required()
            ->validateFor();
    }
}
