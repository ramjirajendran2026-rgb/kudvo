<?php

namespace App\Filament\Member\Pages;

use App\Forms\MemberForm;
use App\Models\Member;
use App\Models\Organisation;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasMaxWidth;
use Filament\Pages\Concerns\HasTopbar;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Guava\FilamentClusters\Forms\Cluster;
use Illuminate\Contracts\Support\Htmlable;

class Profile extends Page
{
    use HasMaxWidth;
    use HasTopbar;
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.member.pages.profile';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getUser()->attributesToArray());
    }

    public function getUser(): Member
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Member) {
            throw new Exception('The authenticated user object must be an Member to allow the profile page to update it.');
        }

        return $user;
    }

    public function hasLogo(): bool
    {
        return true;
    }

    public function save()
    {
        $this->getUser()->update($this->form->getState());

        Notification::make()
            ->success()
            ->title('Profile updated')
            ->send();
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns()
            ->schema([
                MemberForm::membershipNumberComponent()
                    ->disabled()
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn ($rule) => $rule->where('organisation_id', $this->getOrganisation()->getKey())),

                Cluster::make(schema: [
                    MemberForm::titleComponent()
                        ->placeholder(placeholder: __('filament.user.elector-resource.form.title.placeholder')),

                    MemberForm::firstNameComponent()
                        ->columnSpan(2)
                        ->placeholder(placeholder: __('filament.user.elector-resource.form.first_name.placeholder')),

                    MemberForm::lastNameComponent()
                        ->columnSpan(2)
                        ->placeholder(placeholder: __('filament.user.elector-resource.form.last_name.placeholder')),
                ])
                    ->columns(columns: 5)
                    ->label(label: __('filament.user.elector-resource.form.full_name.label')),

                MemberForm::phoneComponent()
                    ->defaultCountry(value: $this->getOrganisation()->country ?: config(key: 'app.default_phone_country'))
                    ->disableLookup()
                    ->initialCountry(value: $this->getOrganisation()->country ?: config(key: 'app.default_phone_country')),

                MemberForm::emailComponent(),
            ]);
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data'),
            ),
        ];
    }

    public function getLayout(): string
    {
        return 'filament-panels::components.layout.simple';
    }

    protected function getLayoutData(): array
    {
        return [
            'hasTopbar' => $this->hasTopbar(),
            'maxWidth' => $this->getMaxWidth(),
        ];
    }

    public function getMaxWidth(): MaxWidth | string | null
    {
        return MaxWidth::ScreenLarge;
    }

    public function getTitle(): string | Htmlable
    {
        return $this->getUser()->full_name;
    }

    public function getHeading(): string | Htmlable
    {
        return filament()->getBrandName();
    }

    public function getOrganisation(): Organisation
    {
        return $this->getUser()->organisation;
    }

    public function getFormActionsAlignment(): string | Alignment
    {
        return Alignment::Start;
    }
}
