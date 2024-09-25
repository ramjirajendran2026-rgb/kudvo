<?php

namespace App\Livewire\Pages;

use App\Forms\Components\OtpInput;
use App\Notifications\QsyssMeetingRegistrationVerificationNotification;
use Closure;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

#[Layout('components.layouts.base')]
#[Title('Membership Registration')]
class QsyssMeetingRegistration extends Component implements HasForms
{
    use InteractsWithForms;
    use WithRateLimiting;

    public ?array $data = [];

    #[Locked]
    public array $lockedData = [];

    #[Locked]
    public ?string $otpHashed = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render()
    {
        return view('livewire.pages.qsyss-meeting-registration')
            ->layoutData([
                'title' => 'Meeting Registration 29 Sep, 2024',
                'seoData' => (new SEOData(
                    title: 'Meeting Registration 29 Sep, 2024',
                    enableTitleSuffix: false,
                ))->markAsNoindex(),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(\App\Models\QsyssMeetingRegistration::class)
            ->schema([
                Section::make('Meeting Registration 29 Sep, 2024')
                    ->schema([
                        TextInput::make('name')
//                            ->disabled(fn () => filled($this->otpHashed))
                            ->maxLength(100)
                            ->required(),

                        PhoneInput::make('phone')
//                            ->hidden(fn () => filled($this->otpHashed))
                            ->disableIpLookUp()
                            ->disallowDropdown()
                            ->initialCountry('IN')
                            ->onlyCountries(['IN'])
                            ->required()
                            ->unique()
                            ->validateFor('IN'),

                        PhoneInput::make('phone')
                            ->visible(fn () => filled($this->otpHashed))
                            ->disabled()
                            ->disableIpLookUp()
                            ->disallowDropdown()
                            ->initialCountry('IN')
                            ->onlyCountries(['IN'])
                            ->required()
                            ->unique()
                            ->validateFor('IN'),

                        Textarea::make('address')
//                            ->disabled(fn () => filled($this->otpHashed))
                            ->maxLength(1000)
                            ->required(),

                        TextInput::make('postal_code')
//                            ->disabled(fn () => filled($this->otpHashed))
                            ->label('Zip / Pin code')
                            ->mask('999999')
                            ->maxLength(100)
                            ->required(),

                        OtpInput::make(name: 'otp')
                            ->hiddenLabel(false)
                            ->length(length: 6)
                            ->required()
                            ->rules([
                                fn (): Closure => function (string $attribute, $value, Closure $fail) {
                                    if (! Hash::check($value, $this->otpHashed)) {
                                        $fail('The :attribute is invalid.');
                                    }
                                },
                            ])
                            ->visible(fn () => filled($this->otpHashed)),

                        Actions::make([
                            Actions\Action::make('cancel')
                                ->action(function () {
                                    $this->otpHashed = null;
                                })
                                ->color('gray')
                                ->visible(fn () => filled($this->otpHashed)),

                            Actions\Action::make('submit')
                                ->action('submit'),
                        ])->alignCenter(),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $e) {
            Notification::make()
                ->title('Too Many Requests')
                ->body('Please try again later.')
                ->danger()
                ->send();

            return;
        }

        $data = $this->form->getState();
        $this->lockedData = $data;

        if (blank($this->otpHashed)) {
            \App\Models\QsyssMeetingRegistration::create($this->lockedData);

            $this->otpHashed = null;
            $this->form->fill();

            $this->js(
                <<<'JS'
Swal.fire({
    title: 'Thank you',
    text: 'Your registration has been submitted successfully',
    icon: 'success'
})
JS
            );

            return;
        }

        $this->lockedData = $data;

        $otp = rand(100000, 999999);
        $this->otpHashed = Hash::make($otp);

        $notification = new QsyssMeetingRegistrationVerificationNotification($otp);

        \Illuminate\Support\Facades\Notification::route('sms', $data['phone'])
            ->notify($notification);
    }

    protected function onValidationError(ValidationException $exception): void
    {
        $failed = $exception->validator->failed();

        if (isset($failed['data.phone']['Unique'])) {
            $exception->validator->getMessageBag()
                ->forget('data.phone');

            $this->form->fill();

            $this->js(
                <<<'JS'
Swal.fire({
    title: 'Thank you',
    text: 'This phone number has been already registered for this meeting',
    icon: 'success'
})
JS
            );
        }
    }
}
