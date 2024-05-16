<?php

namespace App\Forms;

use App\Forms\Components\CountryPicker;
use App\Forms\Components\TimezonePicker;
use App\Models\Organisation;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;

readonly class OrganisationForm
{
    public static function countryComponent(): CountryPicker
    {
        return CountryPicker::make()
            ->label(label: __('filament.user.organisation-resource.form.country.label'))
            ->required();
    }

    public static function logoComponent(): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make(name: 'logo')
            ->collection(collection: Organisation::MEDIA_COLLECTION_LOGO)
            ->extraAttributes(attributes: ['class' => 'org-logo'])
            ->image()
            ->imageEditor()
            ->imageEditorAspectRatios(ratios: ['16:9', '1:1'])
            ->label(label: __('filament.user.organisation-resource.form.logo.label'))
            ->loadingIndicatorPosition('center bottom')
            ->maxSize(size: 500)
            ->panelAspectRatio(ratio: '1:1')
            ->panelLayout(layout: 'compact')
            ->placeholder(placeholder: __('filament.user.organisation-resource.form.logo.placeholder'))
            ->removeUploadedFileButtonPosition(fn (FileUpload $component) => $component->hasImageEditor() ? 'left bottom' : 'center bottom')
            ->uploadButtonPosition(fn (FileUpload $component) => $component->hasImageEditor() ? 'right bottom' : 'center bottom')
            ->uploadProgressIndicatorPosition(fn (FileUpload $component) => $component->hasImageEditor() ? 'right bottom' : 'center bottom');
    }

    public static function nameComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->label(label: __('filament.user.organisation-resource.form.name.label'))
            ->maxLength(length: 60)
            ->minLength(length: 4)
            ->required();
    }

    public static function timezoneComponent(): TimezonePicker
    {
        return TimezonePicker::make()
            ->label(label: __('filament.user.organisation-resource.form.timezone.label'))
            ->required();
    }
}
