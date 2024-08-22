<?php

namespace App\Livewire\Pages;

use App\Data\Home\ClientData;
use App\Data\Home\FeatureCardData;
use App\Data\Home\HeroData;
use App\Data\Home\ProductCardData;
use Livewire\Component;
use RalphJSmit\Laravel\SEO\SchemaCollection;
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
                    enableTitleSuffix: false,
                    schema: SchemaCollection::make()
                        ->add(fn (SEOData $data) => [
                            '@context' => 'https://schema.org',
                            '@type' => 'WebApplication',
                            'name' => config('app.name'),
                            'url' => route('home'),
                            'description' => $data->description,
                            'applicationCategory' => 'BusinessApplication',
                            'operatingSystem' => 'All',
                            'browserRequirements' => 'Requires JavaScript. Compatible with modern web browsers.',
                            'softwareVersion' => '1.0',
                            'inLanguage' => 'en',
                            'author' => [
                                '@type' => 'Organization',
                                'name' => config('app.name'),
                                'url' => route('home'),
                            ],
                            'provider' => [
                                '@type' => 'Organization',
                                'name' => config('app.name'),
                                'url' => route('home'),
                            ],
                            'image' => asset('img/nav-logo.webp'),
                            'contactPoint' => [
                                '@type' => 'ContactPoint',
                                'telephone' => __('app.contact.phone.number'),
                                'email' => __('app.contact.email.address'),
                                'contactType' => 'Customer Support',
                            ],
                            'offers' => [
                                '@type' => 'Offer',
                                'price' => 0.49,
                                'priceCurrency' => 'USD',
                                'url' => route('products.election.home') . '#pricing',
                            ],
                            'aggregateRating' => [
                                '@type' => 'AggregateRating',
                                'ratingValue' => '4.5',
                                'reviewCount' => '864',
                                'bestRating' => '5',
                                'worstRating' => '1',
                            ],
                        ])
                ),
            ]);
    }
}
