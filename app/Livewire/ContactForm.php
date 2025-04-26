<?php

namespace App\Livewire;

use App\Data\ContactFormData;
use App\Mail\ContactFormMail;
use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

/**
 * @property Form $form
 */
class ContactForm extends Component implements HasForms
{
    use InteractsWithForms;
    use WithRateLimiting;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema(components: [
                        TextInput::make(name: 'name')
                            ->label(label: 'Your name')
                            ->maxLength(length: 50)
                            ->required(),

                        TextInput::make(name: 'email')
                            ->email()
                            ->label(label: 'Your email address')
                            ->maxLength(length: 150)
                            ->required()
                            ->rule(rule: 'email:rfc,dns'),

                        Textarea::make(name: 'message')
                            ->autosize()
                            ->label(label: 'Your message')
                            ->minLength(length: 30)
                            ->required(),

                        Turnstile::make(name: 'captcha')
                            ->theme(theme: 'light'),
                    ]),
            ])
            ->statePath(path: 'data');
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="contact-form flex items-center justify-center w-full h-full">
            Preparing a contact form...
        </div>
        HTML;
    }

    public function submit(): void
    {
        try {
            $this->rateLimit(maxAttempts: 5);
        } catch (TooManyRequestsException $e) {
            Notification::make()
                ->title(title: 'Too many requests!')
                ->body(body: 'Please try again in ' . $e->secondsUntilAvailable . ' seconds.')
                ->danger()
                ->send();

            return;
        }

        $data = ContactFormData::from($this->form->getState());

        Mail::to(users: __('app.contact.email.address'))
            ->send(mailable: new ContactFormMail(data: $data));

        Notification::make()
            ->title(title: 'Thanks for your message!')
            ->body(body: 'We will get back to you as soon as possible.')
            ->success()
            ->send();

        $this->form->fill([]);
    }

    protected function onValidationError(ValidationException $exception): void
    {
        $this->dispatch('reset-captcha');
    }
}
