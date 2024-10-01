<?php

namespace App\Forms\Components;

use App\Data\Election\PreferenceData;
use App\Data\Election\VoteSecretData;
use App\Facades\Kudvo;
use App\Models\Candidate;
use App\Models\Position;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Concerns\CanLimitItemsLength;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Spatie\MediaLibrary\MediaCollections\HtmlableMedia;

class VotesPicker extends CheckboxList
{
    use CanLimitItemsLength;

    protected string $view = 'forms.components.votes-picker';

    #[Locked]
    public string $uuid;

    #[Locked]
    public PreferenceData $preference;

    #[Locked]
    public bool $isBoothDevice = false;

    protected ?array $groups = null;

    public static function forPosition(string $uuid, PreferenceData $preference): static
    {
        $static = app(abstract: static::class, parameters: ['name' => $uuid]);

        $static->uuid = $uuid;
        $static->preference = $preference;

        $static->isBoothDevice = Kudvo::isBoothDevice();

        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columns([
            'md' => 2,
            'xl' => 3,
            '2xl' => 4,
        ]);

        $this->descriptions(fn () => $this->getCandidates()->mapWithKeys(fn (Candidate $candidate) => [$candidate->uuid => $candidate->candidateGroup?->short_name])->toArray());

        $this->gridDirection('row');

        $this->hiddenLabel();
        $this->label(fn (Position $position) => $position->name);

        $this->maxItems(fn (Position $position) => $position->quota);
        $this->minItems(fn (Position $position) => $position->threshold);

        $this->mutateDehydratedStateUsing(fn (array $state) => Arr::map($state, fn ($item) => new VoteSecretData($item, 1)));

        $this->noSearchResultsMessage('No candidates match your search.');

        $this->options(fn () => $this->getCandidates()->pluck('display_name', 'uuid')->toArray());

        $this->searchable(fn () => ! $this->isBoothDevice && $this->getCandidates()->count() > 10);

        $this->validationMessages([
            'min' => fn (Position $position) => 'Choose at least ' . $position->threshold . ' ' . Str::plural('candidate', $position->threshold),
            'max' => fn (Position $position) => 'Choose at most ' . $position->quota . ' ' . Str::plural('candidate', $position->quota),
        ]);
    }

    protected function getCacheKeyPrefix(): string
    {
        return 'lw.' . $this->getLivewire()->getId() . '.' . $this->getId() . '.';
    }

    public function getPosition(): Position
    {
        return Cache::remember(
            key: $this->getCacheKeyPrefix() . 'position',
            ttl: 60 * 60 * 24,
            callback: fn () => Position::where('uuid', $this->uuid)->firstOrFail()
        );
    }

    public function getHeading(): string
    {
        return $this->getPosition()->name;
    }

    public function getSubheading(): string
    {
        return $this->getPosition()->abstain ?
            $this->getAbstainHelperText() :
            $this->getNonAbstainHelperText();
    }

    /**
     * @return EloquentCollection<int, Candidate>
     */
    public function getCandidates(): EloquentCollection
    {
        return Cache::remember(
            key: $this->getCacheKeyPrefix() . 'candidates',
            ttl: 60 * 60 * 24,
            callback: fn () => $this->getPosition()
                ->getOrderedCandidates(sort: $this->preference->candidate_sort)
                ->loadMissing('media')
                ->when(
                    $this->hasGroups(),
                    fn (EloquentCollection $collection) => $collection->loadMissing('candidateGroup'),
                )
        );
    }

    public function getCandidate(string $uuid): ?Candidate
    {
        return $this->getCandidates()->firstWhere('uuid', $uuid);
    }

    public function getGroupId(string $uuid): ?int
    {
        return $this->getCandidate($uuid)?->candidate_group_id;
    }

    public function getAbstainHelperText(): string
    {
        return sprintf(
            'Choose %d to %d %s',
            $this->getPosition()->threshold,
            $this->getPosition()->quota,
            Str::plural('candidate', $this->getPosition()->quota)
        );
    }

    public function getNonAbstainHelperText(): string
    {
        return sprintf(
            'Choose %d %s',
            $this->getPosition()->quota,
            Str::plural('candidate', $this->getPosition()->quota)
        );
    }

    public function shouldShowPhoto(): bool
    {
        return $this->preference->candidate_symbol;
    }

    public function getPhotoImg(string $uuid): HtmlableMedia | HtmlString | null
    {
        $candidate = $this->getCandidate(uuid: $uuid);
        $media = $candidate->getFirstMedia(Candidate::MEDIA_COLLECTION_PHOTO);

        if (! $media) {
            $src = $candidate->photo_url;
            $class = Arr::toCssClasses($this->getPhotoClasses());

            return new HtmlString(
                <<<BLADE
<img alt="$candidate->display_name's photo" class="$class" src="$src" />
BLADE
            );
        }

        return $candidate
            ->getFirstMedia(Candidate::MEDIA_COLLECTION_PHOTO)
            ?->img(extraAttributes: [
                'alt' => $candidate->display_name . '\'s photo',
                'class' => $this->getPhotoClasses(),
            ]);
    }

    public function getPhotoClasses(): array
    {
        return ['shrink-0 size-8 rounded-full object-cover object-center md:size-12 lg:size-16 print:hidden'];
    }

    public function shouldShowSymbol(): bool
    {
        return $this->preference->candidate_symbol;
    }

    public function getSymbolImg(string $uuid): HtmlableMedia | HtmlString | null
    {
        $candidate = $this->getCandidate(uuid: $uuid);
        $media = $candidate->getFirstMedia(Candidate::MEDIA_COLLECTION_SYMBOL);

        if (! $media) {
            $src = $candidate->symbol_url;
            $class = Arr::toCssClasses($this->getSymbolClasses());

            return new HtmlString(
                <<<BLADE
<img alt="$candidate->display_name's symbol" class="$class" src="$src" />
BLADE
            );
        }

        return $candidate
            ->getFirstMedia(Candidate::MEDIA_COLLECTION_SYMBOL)
            ?->img(extraAttributes: [
                'alt' => $candidate->display_name . '\'s symbol',
                'class' => $this->getSymbolClasses(),
            ]);
    }

    public function getSymbolClasses(): array
    {
        return ['shrink-0 size-8 rounded object-cover object-center md:size-12 lg:size-16'];
    }

    public function groups(array $groups): static
    {
        $this->groups = $groups;

        return $this;
    }

    public function getGroups(): ?array
    {
        return $this->groups;
    }

    public function hasGroups(): bool
    {
        return $this->getGroups() !== null;
    }

    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        return match ($parameterType) {
            Position::class => [$this->getPosition()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
