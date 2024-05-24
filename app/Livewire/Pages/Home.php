<?php

namespace App\Livewire\Pages;

use App\Data\Home\ClientData;
use App\Data\Home\FeatureCardData;
use App\Data\Home\HeroData;
use App\Data\Home\ProductCardData;
use Livewire\Component;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Home extends Component
{
    public function render()
    {
        return view('livewire.pages.home')
            ->with(key: [
                'featureItems' => collect(__('pages/home.content.features.items'))
                    ->map(fn ($item) => FeatureCardData::from($item)),
                'productItems' => collect(__('pages/home.content.products.items'))
                    ->map(fn ($item) => ProductCardData::from($item)),
                'clientItems' => collect(__('pages/home.content.clientele.items'))
                    ->map(fn ($item) => ClientData::from($item)),
                'heroItems' => collect(__('pages/home.content.hero.items'))
                    ->map(fn ($item) => HeroData::from($item)),
            ])
            ->layoutData(data: [
                'seoData' => new SEOData(
                    title: __('pages/home.seo.title'),
                    description: __('pages/home.seo.description'),
                    enableTitleSuffix: false
                ),
            ]);
    }
}
