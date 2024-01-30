<?php

namespace App\Forms\Components;

use App\Models\Candidate;
use App\Models\Position;
use Closure;
use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Concerns\CanLimitItemsLength;
use Filament\Forms\Components\Concerns\HasPlaceholder;
use Filament\Support\Concerns\HasHeading;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Table\Concerns\HasEmptyState;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Color\Rgb;

class VotePicker extends CheckboxList
{
    use CanLimitItemsLength;
    use HasHeading;
    use HasPlaceholder;

    protected Position $position;

    protected string $view = 'forms.components.vote-picker';

    protected string | Htmlable | Closure | null $description = null;

    protected bool | Closure $preview = false;

    public static function makeFor(Position $position): static
    {
        $static = app(abstract: static::class, parameters: ['name' => $position->uuid]);

        $static->position(position: $position);

        $static->configure();

        return $static;
    }

    public function position(Position $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function preview(bool|Closure $condition = true): static
    {
        $this->preview = $condition;

        return $this;
    }

    public function isPreview(): bool
    {
        return $this->evaluate(value: $this->preview);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $position = $this->position;

        $this->description(
            description: $position->abstain ?
                "Choose".
                ($position->threshold ? " at least $position->threshold and" : "").
                    " upto $position->quota ".
                    Str::plural('candidate', $position->quota) :
                "Choose exactly $position->quota ".Str::plural("candidate", $position->quota)
        );
        $this->hiddenLabel();
        $this->validationAttribute(label: $position->name);

        $this->heading(heading: $position->name);
        $this->placeholder(placeholder: fn (self $component): string => $component->isPreview() ? 'None selected' : 'No candidates');

        $this->options(
            options: fn (string $operation, array $state, self $component) => $position
                ->candidates
                ->when(
                    value: $component->isPreview(),
                    callback: fn (Collection $collection) => $collection->whereIn(
                        key: 'uuid',
                        values: $state,
                    )
                )
                ->mapWithKeys(callback: fn(Candidate $candidate) => [$candidate->uuid => $candidate->display_name])
        );
        $this->searchable(condition: $position->candidates->count() > 2);
        $this->maxItems(count: $position->quota);
        $this->minItems(count: $position->threshold);

        $this->validationMessages(messages: [
            'max' => fn (self $component): ?string => $component->getSectionDescription(),
            'min' => fn (self $component): ?string => $component->getSectionDescription(),
        ]);

        $this->mutateDehydratedStateUsing(callback: fn ($state) => Arr::map($state, fn ($item) => ['key' => $item, 'value' => 1]));
    }

    public function description(string | Htmlable | Closure | null $description = null): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSectionDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->description);
    }

    public function getCandidate(string $uuid): ?Candidate
    {
        return $this->position->candidates->firstWhere('uuid', $uuid);
    }

    public function getPhotoUrl(string $uuid): ?string
    {
        return $this->getCandidate(uuid: $uuid)->photo_url;
    }

    public function getSymbolUrl(string $uuid): ?string
    {
        return $this->getCandidate(uuid: $uuid)->symbol_url;
    }
}
