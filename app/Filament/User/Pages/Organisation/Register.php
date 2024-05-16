<?php

namespace App\Filament\User\Pages\Organisation;

use App\Enums\OrganisationUserRole;
use App\Forms\OrganisationForm;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Database\Eloquent\Model;

class Register extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('filament.user.pages.organisation.register.label');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                OrganisationForm::nameComponent(),

                OrganisationForm::countryComponent(),

                OrganisationForm::timezoneComponent(),
            ]);
    }

    public function getRegisterFormAction(): Action
    {
        return parent::getRegisterFormAction()
            ->label(label: __('filament.user.pages.organisation.register.form.actions.register.label'));
    }

    protected function handleRegistration(array $data): Model
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        return $user->organisations()
            ->create(
                attributes: $data,
                joining: ['role' => OrganisationUserRole::ADMIN]
            );
    }
}
