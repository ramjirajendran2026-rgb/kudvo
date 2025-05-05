<?php

namespace App\Filament\User\Resources\SurveyResource\Pages;

use App\Filament\User\Resources\SurveyResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditSurvey extends EditRecord
{
    protected static string $resource = SurveyResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';

    protected static ?string $navigationLabel = 'Questions';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->hidden();
    }

    public function getRecordTitle(): string | Htmlable
    {
        return 'Survey #' . $this->getRecord()->getKey();
    }

    protected function getHeaderActions(): array
    {
        return [
            SurveyResource::getSettingsAction(),

            SurveyResource::getCopyLinkAction()
                ->outlined(),

            SurveyResource::getShareAction()
                ->outlined(),

            SurveyResource::getPublishAction(),

            SurveyResource::getPreviewAction()
                ->extraAttributes([
                    'wire:target' => 'data',
                    'wire:dirty.class' => 'hidden',
                ]),

            $this->getSaveFormAction()
                ->extraAttributes([
                    'class' => 'hidden',
                    'wire:target' => 'data',
                    'wire:dirty.class.remove' => 'hidden',
                ])
                ->formId('form')
                ->icon('heroicon-m-check')
                ->label('Save'),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return $this->getRecordTitle();
    }
}
