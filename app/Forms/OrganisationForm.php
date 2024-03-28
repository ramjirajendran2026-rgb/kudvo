<?php

namespace App\Forms;

use App\Forms\Components\CountryPicker;
use App\Forms\Components\TimezonePicker;
use App\Models\Organisation;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;

readonly class OrganisationForm
{
    public static function countryComponent(): CountryPicker
    {
        return CountryPicker::make()
            ->label(label: 'Country')
            ->required();
    }

    public static function logoComponent(): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make(name: 'logo')
            ->collection(collection: Organisation::MEDIA_COLLECTION_LOGO)
            ->image()
            ->imageEditor()
            ->imageEditorAspectRatios([
                '16:9',
                '4:3',
                '1:1',
            ])
            ->imageResizeMode(mode: 'cover')
            ->imageCropAspectRatio(ratio: '1:1')
            ->imageResizeTargetWidth(width: '512')
            ->imageResizeTargetHeight(height: '512');
    }

    public static function nameComponent(): TextInput
    {
        return TextInput::make(name: 'name')
            ->label(label: 'Organisation name')
            ->maxLength(length: 60)
            ->minLength(length: 4)
            ->required();
    }

    public static function timezoneComponent(): TimezonePicker
    {
        return TimezonePicker::make()
            ->label(label: 'Timezone')
            ->required();
    }
}
