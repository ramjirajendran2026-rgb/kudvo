<?php

namespace App\Livewire\Pages\Products\Meeting;

use Livewire\Component;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Home extends Component
{
    public function render()
    {
        return view('livewire.pages.products.meeting.home')
            ->layoutData(data: [
                'seoData' => new SEOData(
                    title: __('pages/products/meeting/home.seo.title'),
                    description: __('pages/products/meeting/home.seo.description'),
                    schema: SchemaCollection::make()
                        ->add(fn (SEOData $data): array => [
                            '@context' => 'https://schema.org',
                            '@type' => 'BreadcrumbList',
                            'itemListElement' => [
                                [
                                    '@type' => 'ListItem',
                                    'position' => 1,
                                    'name' => 'Home',
                                    'item' => config('app.url'),
                                ],
                                [
                                    '@type' => 'ListItem',
                                    'position' => 2,
                                    'name' => 'Resolution Voting',
                                ],
                            ],
                        ]),
                ),
            ]);
    }
}
