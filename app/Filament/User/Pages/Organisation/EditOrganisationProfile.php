<?php

namespace App\Filament\User\Pages\Organisation;

use App\Forms\OrganisationForm;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Support\Enums\MaxWidth;

class EditOrganisationProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Organisation Profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                Section::make()
                    ->maxWidth(width: MaxWidth::TwoExtraLarge)
                    ->schema(components: [
                        OrganisationForm::nameComponent(),

                        OrganisationForm::countryComponent(),

                        OrganisationForm::timezoneComponent(),
                    ]),
            ]);
    }

    protected function getRedirectUrl(): ?string
    {
        return Filament::getUrl();
    }
}
