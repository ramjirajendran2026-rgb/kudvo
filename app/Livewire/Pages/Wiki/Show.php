<?php

namespace App\Livewire\Pages\Wiki;

use App\Models\WikiCategory;
use App\Models\WikiPage;
use App\Models\WikiTag;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public WikiPage $page;

    public function render()
    {
        return view('livewire.pages.wiki.show')
            ->layoutData([
                'seoData' => $this->page,
            ]);
    }

    #[Computed]
    public function defaultCoverUrl(): string
    {
        return WikiPage::getDefaultCoverUrl();
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
}
