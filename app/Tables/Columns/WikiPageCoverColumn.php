<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class WikiPageCoverColumn extends SpatieMediaLibraryImageColumn
{
    protected string $view = 'tables.columns.wiki-page-cover-column';

    protected function setUp(): void
    {
        parent::setUp();

        $this->height('auto');
        $this->width('100%');
    }

    public function getImageSrcset(?string $state = null): ?string
    {
        $record = $this->getRecord();

        if ($this->hasRelationship($record)) {
            $record = $this->getRelationshipResults($record);
        }

        $records = Arr::wrap($record);

        foreach ($records as $record) {
            /** @var Model $record */

            /** @var ?Media $media */
            $media = $record->getRelationValue('media')->first(fn (Media $media): bool => $media->uuid === $state);

            if (! $media) {
                continue;
            }

            $conversion = $this->getConversion();

            if ($this->getVisibility() === 'private') {
                try {
                    return $media->getTemporaryUrl(
                        now()->addMinutes(5),
                        $conversion ?? '',
                    );
                } catch (Throwable $exception) {
                    // This driver does not support creating temporary URLs.
                }
            }

            return $media->getSrcset($conversion ?? '');
        }

        return null;
    }
}
