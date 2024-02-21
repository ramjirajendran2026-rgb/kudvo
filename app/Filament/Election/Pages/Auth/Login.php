<?php

namespace App\Filament\Election\Pages\Auth;

use App\Enums\OneTimePasswordPurpose;
use App\Facades\Kudvo;
use App\Filament\Election\Http\Middleware\EnsureStateIsAllowed;
use App\Filament\Election\Pages\Mfa\Notice;
use App\Models\Election;
use App\Models\Elector;
use App\Notifications\Election\MfaCodeNotification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BasePage;
use Filament\Panel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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

        if (! $elector->canAccessPanel(Filament::getCurrentPanel())) {
            $this->throwFailureValidationException();
        }

        if ($elector->ballot?->isVoted() && !Kudvo::getElection()?->preference?->voted_ballot_update) {
            Notification::make()
                ->title(title: 'Already voted')
                ->warning()
                ->send();

            $this->redirect(url: Filament::getLoginUrl(), navigate: Filament::getCurrentPanel()->hasSpaMode());

            return null;
        }

        static::doLogin(elector: $elector, panel: Filament::getCurrentPanel(), request: request());

        if (Kudvo::getElection()->isMfaRequired()) {
            $this->sendMfaCode(elector: $elector);
        }

        return app(abstract: LoginResponse::class);
    }

    public static function doLogin(Elector $elector, Panel $panel, Request $request): void
    {
        $panel->auth()->login(user: $elector);
        session()->regenerate();

        $elector->createAuthSession(
            sessionId: session()->getId(),
            guardName: $panel->getAuthGuard(),
            request: $request,
        );
    }

    protected function sendMfaCode(Elector $elector): void
    {
        $oneTimePassword = $elector
            ->oneTimePasswords()
            ->create(attributes: [
                'purpose' => OneTimePasswordPurpose::MFA,

                ...Kudvo::getElection()->preference->mfa_sms ? ['phone' => $elector->phone] : [],
                ...Kudvo::getElection()->preference->mfa_mail ? ['email' => $elector->email] : [],
            ]);

        $oneTimePassword->send(
            notification: new MfaCodeNotification(
                election: Kudvo::getElection(),
                oneTimePassword: $oneTimePassword,
            )
        );

        Session::put(key: Notice::getMfaSessionKey(elector: $elector), value: $oneTimePassword->getKey());
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

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label(label: Kudvo::getElection()->isMfaRequired() ? 'Get OTP' : 'Sign in');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                $this->getPhoneComponent()
                    ->initialCountry(value: Kudvo::getOrganisation()?->country),

                $this->getMfaConsentComponent(),
            ]);
    }

    protected function getPhoneComponent()
    {
        return PhoneInput::make(name: 'phone')
            ->label(label: 'Your phone number')
            ->required()
            ->validateFor();
    }

    protected function getMfaConsentComponent()
    {
        return Checkbox::make(name: 'consent')
            ->accepted()
            ->label(label: 'I agree to receive OTP (One Time Password)')
            ->validationAttribute(label: 'consent')
            ->visible(condition: Kudvo::getElection()->isMfaRequired());
    }
}
