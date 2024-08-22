<?php

namespace App\Livewire\Pages\Wiki;

use App\Models\WikiCategory;
use App\Models\WikiPage;
use App\Models\WikiTag;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Concerns\HasTabs;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use RalphJSmit\Laravel\SEO\Schema\BreadcrumbListSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Index extends Component implements HasForms, HasTable
{
    use HasTabs;
    use InteractsWithForms;
    use InteractsWithTable;

    #[Url]
    public ?string $activeCategory = null;

    #[Url]
    public ?string $activeTag = null;

    public function render(): View
    {
        return view('livewire.pages.wiki.index')
            ->layoutData([
                'seoData' => new SEOData(
                    title: 'Wiki Articles',
                    schema: SchemaCollection::make()
                        ->addBreadcrumbs(
                            fn (BreadcrumbListSchema $schema, SEOData $data): BreadcrumbListSchema => $schema
                                ->prependBreadcrumbs([
                                    'Home' => route('home'),
                                ])
                        ),
                ),
            ]);
    }

    #[Computed]
    public function defaultCoverUrl(): string
    {
        return WikiPage::getDefaultCoverUrl();
    }

    #[Computed]
    public function latestPage(): ?WikiPage
    {
        return WikiPage::with(['category', 'tags'])
            ->latest()
            ->first();
    }

    /**
     * @return Collection<int, WikiCategory>
     */
    #[Computed]
    public function categories(): Collection
    {
        return WikiCategory::orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, WikiTag>
     */
    #[Computed]
    public function tags(): Collection
    {
        return WikiTag::inRandomOrder()
            ->limit(15)
            ->get();
    }

    public function updated(string $property): void
    {
        match ($property) {
            'activeCategory',
            'activeTag' => $this->resetPage(),
            default => null,
        };
    }

    public function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
            ])
            ->columns([
                Stack::make([
                    SpatieMediaLibraryImageColumn::make('cover')
                        ->collection(WikiPage::MEDIA_COLLECTION_COVER)
                        ->view('tables.columns.wiki-page-cover-column'),

                    TextColumn::make('category.name')
                        ->badge()
                        ->extraAttributes([
                            'class' => 'cell-category',
                        ]),

                    TextColumn::make('title')
                        ->extraAttributes([
                            'class' => 'cell-title',
                        ])
                        ->size(TextColumn\TextColumnSize::Large),

                    TextColumn::make('summary')
                        ->extraAttributes([
                            'class' => 'cell-summary',
                        ])
                        ->lineClamp(3),
                ])
                    ->space(2),
            ])
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->when(
                        $this->activeCategory,
                        fn (Builder $query, string $value) => $query
                            ->whereBelongsTo($this->categories->firstWhere('slug', $value), 'category')
                    )
                    ->when(
                        $this->activeTag,
                        fn (Builder $query, string $value) => $query
                            ->whereHas(
                                'tags',
                                fn (Builder $query) => $query->where('slug', $value)
                            )
                    )
            )
            ->recordUrl(fn (WikiPage $page) => route('wiki.show', [$page]))
            ->query(
                WikiPage::query()
                    ->when(
                        $this->latestPage,
                        fn (Builder $query, $value) => $query->whereKeyNot($value)
                    )
            );
    }
}
