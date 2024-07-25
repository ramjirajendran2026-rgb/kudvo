<?php

namespace App\Filament\Election\Pages\Auth;

use App\Actions\Election\Booth\UpdateOnElectorLogin;
use App\Enums\OneTimePasswordPurpose;
use App\Events\ElectorAssignedToBoothEvent;
use App\Facades\Kudvo;
use App\Filament\Election\Pages\Mfa\Notice;
use App\Models\Election;
use App\Models\Elector;
use App\Notifications\Election\MfaCodeNotification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
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
    public function getListeners(): array
    {
        $listeners = parent::getListeners();

        if (Kudvo::isBoothDevice()) {
            $listeners['echo:election-booth.' . Kudvo::getElectionBoothToken()?->getKey() . ',.' . ElectorAssignedToBoothEvent::getBroadcastName()] = 'electorAssignedToBooth';
        }

        return $listeners;
    }

    public function electorAssignedToBooth(): ?LoginResponse
    {
        $boothToken = Kudvo::getElectionBoothToken();

        $elector = $boothToken?->currentElector;

        if (blank($elector)) {
            return null;
        }

        return $this->proceedWithElector($elector);
    }

    public function getHeading(): string | Htmlable
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

        return $this->proceedWithElector($elector);
    }

    protected function proceedWithElector(Elector $elector): ?LoginResponse
    {
        if (! $elector->canAccessPanel(Filament::getCurrentPanel())) {
            $this->throwFailureValidationException();
        }

        if ($elector->ballot?->isVoted() && ! Kudvo::getElection()?->preference?->voted_ballot_update) {
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

        UpdateOnElectorLogin::execute(elector: $elector);
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
            ->visible(condition: $this->isBoothSelfLoginAllowed())
            ->label(
                label: Kudvo::getElection()->isMfaRequired() ?
                    __('filament.election.pages.auth.login.form.actions.authenticate.get_otp_label') :
                    __('filament.election.pages.auth.login.form.actions.authenticate.sign_in_label')
            );
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(condition: ! $this->isBoothSelfLoginAllowed())
            ->schema(
                components: [
                    Group::make()
                        ->schema(components: [
                            $this->getPhoneComponent()
                                ->initialCountry(value: Kudvo::getOrganisation()?->country),

                            $this->getMfaConsentComponent(),
                        ])
                        ->visible($this->isBoothSelfLoginAllowed()),
                ],
            );
    }

    protected function getPhoneComponent()
    {
        return PhoneInput::make(name: 'phone')
            ->label(label: __('filament.election.pages.auth.login.form.phone.label'))
            ->required()
            ->validateFor();
    }

    protected function getMfaConsentComponent()
    {
        return Checkbox::make(name: 'consent')
            ->accepted()
            ->default(state: true)
            ->label(label: __('filament.election.pages.auth.login.form.consent.label'))
            ->validationAttribute(label: 'consent')
            ->visible(condition: Kudvo::getElection()->isMfaRequired());
    }

    protected function isBoothSelfLoginAllowed(): bool
    {
        return ! Kudvo::isBoothDevice() || Kudvo::getElection()->booth_preference?->login_by_self;
    }
}
