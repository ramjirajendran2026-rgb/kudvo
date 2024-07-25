<?php

namespace App\Filament\Election\Pages\Mfa;

use App\Enums\OneTimePasswordPurpose;
use App\Filament\Base\Contracts\HasElection;
use App\Filament\Election\Http\Middleware\EnsureMfaCompleted;
use App\Filament\Election\Pages\Concerns\InteractsWithElection;
use App\Filament\Election\Pages\Concerns\InteractsWithElector;
use App\Models\Elector;
use App\Models\OneTimePassword;
use App\Notifications\Election\MfaCodeNotification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;

/**
 * @property Form $form
 */
class Notice extends Page implements HasElection
{
    use InteractsWithElection;
    use InteractsWithElector;
    use InteractsWithFormActions;
    use WithRateLimiting;

    protected static string $view = 'filament.election.pages.mfa.notice';

    protected static ?string $slug = 'mfa/notice';

    protected static string | array $withoutRouteMiddleware = EnsureMfaCompleted::class;

    protected static bool $shouldRegisterNavigation = false;

    public static string | Alignment $formActionsAlignment = Alignment::Center;

    public ?array $data = [];

    public function mount(): void
    {
        if (! $this->getElection()->isMfaRequired() || $this->getElector()->authSession?->isMfaCompleted()) {
            $this->redirect(url: Filament::getUrl(), navigate: $this->isSpa());

            return;
        }

        if (
            Session::has(key: static::getMfaSessionKey($this->getElector())) &&
            OneTimePassword::find(id: Session::get(key: static::getMfaSessionKey($this->getElector())))
        ) {
            $this->redirect(Verify::getUrl(), navigate: $this->isSpa());

            return;
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                Section::make(heading: __('filament.election.pages.mfa.notice.form.heading'))
                    ->extraAttributes(attributes: ['class' => 'text-center'])
                    ->schema(components: [
                        Placeholder::make(name: 'description')
                            ->content(content: $this->getNoticeText())
                            ->extraAttributes(attributes: ['class' => 'text-start'])
                            ->hiddenLabel(),

                        Checkbox::make(name: 'consent')
                            ->accepted()
                            ->label(label: __('filament.election.pages.mfa.notice.form.consent.label'))
                            ->validationAttribute(label: 'consent'),
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
                ->label(label: __('filament.election.pages.mfa.notice.form.actions.submit.label'))
                ->submit(form: 'submit'),
        ];
    }

    public function submit()
    {
        $this->form->getState();

        try {
            $this->rateLimit(maxAttempts: 3);
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

        $oneTimePassword = $this->getElector()
            ->oneTimePasswords()
            ->create(attributes: [
                'purpose' => OneTimePasswordPurpose::MFA,

                ...$this->getElection()->preference->mfa_sms ? ['phone' => $this->getElector()->phone] : [],
                ...$this->getElection()->preference->mfa_mail ? ['email' => $this->getElector()->email] : [],
            ]);

        $oneTimePassword->send(
            notification: new MfaCodeNotification(
                election: $this->getElection(),
                oneTimePassword: $oneTimePassword,
            )
        );

        Session::put(key: static::getMfaSessionKey($this->getElector()), value: $oneTimePassword->getKey());

        $this->redirect(Verify::getUrl(), navigate: $this->isSpa());
    }

    public function isSpa(): bool
    {
        return Filament::getCurrentPanel()->hasSpaMode();
    }

    protected function getNoticeText(): string
    {
        $via = [
            ...$this->getElection()->preference->mfa_sms ? ['phone number'] : [],
            ...$this->getElection()->preference->mfa_mail ? ['email address'] : [],
        ];

        return __('filament.election.pages.mfa.notice.form.notice.content', ['via' => Arr::implodeWithAnd($via)]);
    }

    public static function getMfaSessionKey(Elector $elector): string
    {
        return implode(
            separator: '_',
            array: [
                Filament::getAuthGuard(),
                'mfa',
                $elector->getKey(),
            ]
        );
    }
}
