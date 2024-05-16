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
        return __('filament.user.pages.organisation.edit.label');
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(columns: 5)
            ->schema(components: [
                Section::make()
                    ->columnSpan(span: 4)
                    ->schema(components: [
                        OrganisationForm::nameComponent(),

                        OrganisationForm::countryComponent(),

                        OrganisationForm::timezoneComponent(),
                    ]),

                OrganisationForm::logoComponent()
                    ->hiddenLabel(),
            ]);
    }

    protected function getRedirectUrl(): ?string
    {
        return Filament::getUrl();
    }
}
