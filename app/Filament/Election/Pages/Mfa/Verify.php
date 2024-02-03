<?php

namespace App\Filament\Election\Pages\Mfa;

use App\Facades\Kudvo;
use App\Filament\Contracts\HasElection;
use App\Filament\Election\Http\Middleware\EnsureMfaCompleted;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Filament\Election\Pages\Concerns\InteractsWithElector;
use App\Forms\Components\OtpInput;
use App\Models\OneTimePassword;
use App\Notifications\ElectionMfaNotification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Features\SupportRedirects\Redirector;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @property Form $form
 */
class Verify extends Page implements HasElection
{
    use InteractsWithElector;
    use InteractsWithElection;
    use WithRateLimiting;

    protected static string $view = 'filament.election.pages.mfa.verify';

    protected static ?string $slug = 'mfa/verify';

    protected static string | array $withoutRouteMiddleware = EnsureMfaCompleted::class;

    protected static bool $shouldRegisterNavigation = false;

    protected ?string $subheading = 'Multi-Factor Authentication';

    public static string | Alignment $formActionsAlignment = Alignment::Center;

    public ?array $data = [];

    #[Locked]
    public ?OneTimePassword $oneTimePassword;

    public bool $spaMode;

    public function mount(Agent $agent): void
    {
        $this->spaMode = Filament::getCurrentPanel()->hasSpaMode();

        if (
            $this->getElector()->authSession?->isMfaCompleted() ||
            ! $this->getElection()->isMfaRequired()
        ) {
            $this->redirect(url: Filament::getUrl(), navigate: $this->spaMode);

            return;
        }

        if (! Session::has(key: Notice::getMfaSessionKey($this->getElector()))) {
            $this->redirect(url: Notice::getUrl(), navigate: $this->spaMode);

            return;
        }

        $this->oneTimePassword = OneTimePassword::find(id: Session::get(key: Notice::getMfaSessionKey($this->getElector())));

        if ($this->oneTimePassword?->isVerified()) {
            $this->redirect(url: Filament::getUrl(), navigate: $this->spaMode);

            return;
        }

        if (blank(value: $this->oneTimePassword) || $this->oneTimePassword->isExpired()) {
            Session::remove(key: Notice::getMfaSessionKey(elector: $this->getElector()));

            $this->redirect(url: Notice::getUrl(), navigate: $this->spaMode);

            return;
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->extraAttributes(attributes: [
                'class' => 'max-w-lg mx-auto',
            ])
            ->schema(components: [
                Section::make(heading: 'MFA Verification')
                    ->headerActions(actions: [
                        Actions\Action::make(name: 'resend')
                            ->action(action: 'resend')
                            ->link(),
                    ])
                    ->schema(components: [
                        Placeholder::make(name: 'description')
                            ->content(content: $this->getNoticeText())
                            ->extraAttributes(attributes: [
                                'class' => 'text-center'
                            ])
                            ->hiddenLabel(),

                        OtpInput::make(name: 'code')
                            ->afterStateUpdated(callback: fn (?string $state, self $livewire) => $livewire->submit())
                            ->autoFillOnly(condition: ! Kudvo::isBoothDevice() && $this->getElection()->isMfaSmsAutoFillOnly())
                            ->length(length: strlen(string: $this->oneTimePassword->code))
                            ->required(),
                    ]),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                form: $this->makeForm()
                    ->statePath(path: 'data'),
            ),
        ];
    }

    /**
     * @throws Throwable
     */
    public function submit(): void
    {
        try {
            $this->rateLimit(maxAttempts: 3);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(title: 'Too many attempts')
                ->body(body: 'Please try again in '.$exception->secondsUntilAvailable.' seconds.')
                ->danger()
                ->send();

            return;
        }

        $data = $this->form->getState();

        throw_unless(
            condition: $this->oneTimePassword->verify(code: $data['code']),
            exception: ValidationException::withMessages(messages: ['data.code' => 'Invalid code'])
        );

        $this->getElector()->authSession?->touch(attribute: 'mfa_completed_at');

        $this->redirectIntended(default: Filament::getUrl(), navigate: Filament::getCurrentPanel()->hasSpaMode());
    }

    public function resend(): void
    {
        try {
            $this->rateLimit(maxAttempts: 1);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(title: 'Too many requests')
                ->body(body: 'Please try again in '.$exception->secondsUntilAvailable.' seconds.')
                ->danger()
                ->send();

            return;
        }

        $this->oneTimePassword->send(
            notification: new ElectionMfaNotification(
                election: $this->getElection(),
                oneTimePassword: $this->oneTimePassword,
            )
        );
        $this->data['code'] = '';

        Notification::make()
            ->title(title: 'OTP resent')
            ->success()
            ->send();
    }

    protected function getNoticeText(): string
    {
        $via = [
            ...$this->getElection()->preference->mfa_sms ? ['phone number'] : [],
            ...$this->getElection()->preference->mfa_mail ? ['email address'] : [],
        ];

        return '6 digit OTP code has been sent to your '.Arr::implodeWithAnd($via).'.';
    }

    /**
     * @throws Throwable
     */
    #[On(event: 'otp-received')]
    public function verifyOTP(string $code): void
    {
        $this->data['code'] = $code;

        $this->submit();
    }
}
