<?php

namespace App\Filament\Member\Pages\Auth;

use App\Models\Organisation;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BasePage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;

class Login extends BasePage
{
    public ?string $org = null;

    public function mount(): void
    {
        $this->org = request()->get('org');
    }

    #[Computed(persist: true)]
    public function organisation(): ?Organisation
    {
        if (blank($this->org)) {
            return null;
        }

        return Organisation::firstWhere('code', $this->org);
    }

    protected function getFormSchema(): array
    {
        if (blank($this->organisation)) {
            return [
                TextInput::make('organisation_code')
                    ->exists('organisations', 'code')
                    ->required(),
            ];
        }

        return [
            $this->getMembershipNumberFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getRememberFormComponent(),
        ];
    }

    protected function getMembershipNumberFormComponent(): Component
    {
        return TextInput::make('membership_number')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema($this->getFormSchema());
    }

    public function authenticate(): ?LoginResponse
    {
        if (blank($this->organisation)) {
            $data = $this->form->getState();
            $this->org = $data['organisation_code'];

            $this->redirect(Filament::getLoginUrl(['org' => $this->org]));

            return null;
        }

        return parent::authenticate();
    }

    public function getSubheading(): string | Htmlable | null
    {
        return $this->organisation?->name ?? parent::getSubheading();
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label(filled($this->organisation) ? 'Login' : 'Continue');
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'organisation_id' => $this->organisation?->getKey(),
            'membership_number' => $data['membership_number'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.membership_number' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
