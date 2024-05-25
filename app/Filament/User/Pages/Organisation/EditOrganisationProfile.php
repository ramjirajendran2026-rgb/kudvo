<?php

namespace App\Filament\User\Pages\Organisation;

use App\Filament\User\Resources\OrganisationResource;
use App\Forms\OrganisationForm;
use Filament\Actions\LocaleSwitcher;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class EditOrganisationProfile extends EditTenantProfile
{
    use Translatable;

    public static function getLabel(): string
    {
        return __('filament.user.pages.organisation.edit.label');
    }

    public function mount(): void
    {
        parent::mount();

        $this->setActiveLocale(App::getLocale());
    }

    public static function getResource(): string
    {
        return OrganisationResource::class;
    }

    public function getRecord(): Model
    {
        if (blank($this->activeLocale)) {
            return $this->tenant;
        }

        return $this->tenant->setLocale($this->activeLocale);
    }

    protected function getActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function fillFormWithDataAndCallHooks(Model $record, array $extraData = []): void
    {
        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill([
            ...$record->attributesToArray(),
            ...$extraData,
        ]);

        $this->form->fill($data);

        $this->callHook('afterFill');
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
