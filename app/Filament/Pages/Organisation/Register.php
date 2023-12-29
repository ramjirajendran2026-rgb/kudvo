<?php

namespace App\Filament\Pages\Organisation;

use App\Enums\OrganisationUserRoleEnum;
use App\Filament\Forms\OrganisationForm;
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
        return 'Organisation Setup';
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema(components: [
                OrganisationForm::nameComponent(),

                OrganisationForm::countryComponent(),

                OrganisationForm::timezoneComponent(),
            ]);
    }

    public function getRegisterFormAction(): Action
    {
        return parent::getRegisterFormAction()
            ->label(label: 'Finish Setup');
    }

    protected function handleRegistration(array $data): Model
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        return $user->organisations()
            ->create(
                attributes: $data,
                joining: ['role' => OrganisationUserRoleEnum::ADMIN]
            );
    }
}
