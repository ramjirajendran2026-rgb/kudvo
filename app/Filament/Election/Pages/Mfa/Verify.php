<?php

namespace App\Filament\Election\Pages\Mfa;

use App\Filament\Contracts\HasElection;
use App\Filament\Election\Http\Middleware\EnsureMfaCompleted;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Forms\Components\OtpInput;
use App\Models\OneTimePassword;
use App\Notifications\ElectionMfaNotification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
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
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Features\SupportRedirects\Redirector;
use Throwable;

/**
 * @property Form $form
 */
class Verify extends Page implements HasElection
{
    use InteractsWithFormActions;
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

    public function mount(): void
    {
        if (
            $this->getElector()->authSession?->isMfaCompleted() ||
            ! $this->getElection()->isMfaRequired()
        ) {
            $this->redirect(url: Filament::getUrl());

            return;
        }

        if (! Session::has(key: Notice::getMfaSessionKey($this->getElector()))) {
            $this->redirect(url: Notice::getUrl());

            return;
        }

        $this->oneTimePassword = OneTimePassword::find(id: Session::get(key: Notice::getMfaSessionKey($this->getElector())));

        if ($this->oneTimePassword?->isVerified()) {
            $this->redirect(url: Filament::getUrl());

            return;
        }

        if (blank(value: $this->oneTimePassword) || $this->oneTimePassword->isExpired()) {
            Session::remove(key: Notice::getMfaSessionKey(elector: $this->getElector()));

            $this->redirect(url: Notice::getUrl());

            return;
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                Section::make()
                    ->schema(components: [
                        Placeholder::make(name: 'description')
                            ->content(content: $this->getNoticeText())
                            ->extraAttributes(attributes: [
                                'class' => 'text-center'
                            ])
                            ->hiddenLabel(),

                        OtpInput::make(name: 'code')
                            ->length(length: strlen(string: $this->oneTimePassword->code))
                            ->hintAction(
                                action: \Filament\Forms\Components\Actions\Action::make(name: 'resend')
                                    ->action(action: 'resend'),
                            )
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

    public function getFormActions(): array
    {
        return [
            Action::make(name: 'submit')
                ->label(label: 'Verify')
                ->submit(form: 'submit'),
        ];
    }

    /**
     * @throws Throwable
     */
    public function submit(): RedirectResponse|Redirector|null
    {
        try {
            $this->rateLimit(maxAttempts: 3);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(title: 'Too many attempts')
                ->body(body: 'Please try again in '.$exception->secondsUntilAvailable.' seconds.')
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        throw_unless(
            condition: $this->oneTimePassword->verify(code: $data['code']),
            exception: ValidationException::withMessages(messages: ['data.code' => 'Invalid code'])
        );

        $this->getElector()->authSession?->touch(attribute: 'mfa_completed_at');

        return redirect()->intended(default: Filament::getUrl());
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

        return '6 digit OTP code has been sent to your registered '.Arr::implodeWithAnd($via).'.';
    }

    #[On(event: 'otp-received')]
    public function verifyOTP(string $code)
    {
        Notification::make()
            ->title(title: 'Verifying otp is '.$code)
            ->success()
            ->send();
    }
}
