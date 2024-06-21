<?php

namespace App\Forms\Components;

use App\Data\Election\VoteSecretData;
use App\Models\Candidate;
use App\Models\CandidateGroup;
use App\Models\Position;
use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Concerns\CanLimitItemsLength;
use Filament\Forms\Components\Concerns\HasPlaceholder;
use Filament\Support\Concerns\HasHeading;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class VotePicker extends CheckboxList
{
    use CanLimitItemsLength;
    use HasHeading;
    use HasPlaceholder;

    protected Position $position;

    protected string $view = 'forms.components.vote-picker';

    protected string|Htmlable|Closure|null $description = null;

    protected bool|Closure $preview = false;

    protected bool|Closure $photo = false;

    protected bool|Closure $symbol = false;

    protected bool|Closure $candidateGroup = false;

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

    public function photo(bool|Closure $condition = true): static
    {
        $this->photo = $condition;

        return $this;
    }

    public function hasPhoto(): bool
    {
        return (bool) $this->evaluate(value: $this->photo);
    }

    public function symbol(bool|Closure $condition = true): static
    {
        $this->symbol = $condition;

        return $this;
    }

    public function hasSymbol(): bool
    {
        return (bool) $this->evaluate(value: $this->symbol);
    }

    public function candidateGroup(bool|Closure $condition = true): static
    {
        $this->candidateGroup = $condition;

        return $this;
    }

    public function hasCandidateGroup(): bool
    {
        return $this->evaluate(value: $this->candidateGroup) && ! $this->isPreview();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $position = $this->position;

        $this->columns();
        $this->gridDirection(gridDirection: 'row');

        $txtSelected = __('filament.forms.components.vote_picker.general.selected');
        $this->description(
            description: new HtmlString(
                html: $this->getDescriptionHint().
                ' • '.
                '<span class="text-info-500" x-text="checkedOptionsCount+\' '.$txtSelected.'\'"></span>'
            )
        );
        $this->hiddenLabel();
        $this->validationAttribute(label: $position->name);

        $this->heading(heading: $position->name);
        $this->placeholder(
            placeholder: fn (self $component): string => $component->isPreview() ?
                __('filament.forms.components.vote_picker.placeholder.none_selected') :
                __('filament.forms.components.vote_picker.placeholder.no_candidates')
        );

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
                ->mapWithKeys(callback: fn (Candidate $candidate) => [$candidate->uuid => $candidate->display_name])
        );
        $this->descriptions(
            descriptions: fn (string $operation, array $state, self $component) => $position
                ->candidates
                ->when(
                    value: $component->isPreview(),
                    callback: fn (Collection $collection) => $collection->whereIn(
                        key: 'uuid',
                        values: $state,
                    )
                )
                ->when(
                    value: $component->hasCandidateGroup(),
                    callback: fn (Collection $collection) => $collection->load(relations: 'candidateGroup')
                )
                ->mapWithKeys(callback: fn (Candidate $candidate) => [
                    $candidate->uuid => collect(value: [
                        $candidate->membership_number,
                        $component->hasCandidateGroup() ? $candidate->candidateGroup?->name : null,
                    ])
                        ->filter(callback: fn (?string $item): bool => filled($item))
                        ->implode(value: ' • '),
                ])
        );
        $this->bulkToggleable();
        $this->maxItems(count: $position->quota);
        $this->minItems(count: $position->threshold);

        $this->validationMessages(messages: [
            'max' => fn (self $component): ?string => $component->getDescriptionHint(),
            'min' => fn (self $component): ?string => $component->getDescriptionHint(),
        ]);

        $this->mutateDehydratedStateUsing(callback: fn ($state) => Arr::map($state, fn ($item) => new VoteSecretData(key: $item, value: 1)));
    }

    public function description(string|Htmlable|Closure|null $description = null): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSectionDescription(): string|Htmlable|null
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

    public function getDescriptionHint(): string
    {
        $position = $this->position;

        return $position->abstain ?
            'Choose'.
            ($position->threshold ? " at least $position->threshold and" : '').
            " upto $position->quota ".
            Str::plural('candidate', $position->quota) :
            "Choose exactly $position->quota ".Str::plural('candidate', $position->quota);
    }

    public function getCandidateGroupId(string $uuid): ?int
    {
        return $this->getCandidate(uuid: $uuid)->candidate_group_id;
    }

    public function getCandidateGroups()
    {
        return CandidateGroup::query()
            ->whereBelongsTo(related: $this->position->event)
            ->pluck(column: 'short_name', key: 'id')
            ->put(key: 'independent', value: 'Independent')
            ->prepend(value: 'All', key: 'all');
    }
}
