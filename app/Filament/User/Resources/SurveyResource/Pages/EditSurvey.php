<?php

namespace App\Filament\User\Resources\SurveyResource\Pages;

use App\Filament\User\Resources\SurveyResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

class EditSurvey extends EditRecord
{
    protected static string $resource = SurveyResource::class;

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return MaxWidth::ScreenMedium;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getRecordTitle(): string | Htmlable
    {
        return 'Survey #' . $this->getRecord()->getKey();
    }

    public function getTitle(): string | Htmlable
    {
        return $this->getRecordTitle();
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->alpineClickHandler('window.location.href = ' . Js::from(static::getResource()::getUrl()));
    }

    protected function getHeaderActions(): array
    {
        return [
            SurveyResource::getSettingsAction(),

            SurveyResource::getCopyLinkAction()
                ->outlined(),

            SurveyResource::getShareAction()
                ->outlined(),

            SurveyResource::getPreviewAction(),

            SurveyResource::getPublishAction(),

            SurveyResource::getResponsePageAction(),
        ];
    }
}
