<?php

namespace App\Livewire\Pages\Products\Election;

use Livewire\Component;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Home extends Component
{
    public function render()
    {
        return view('livewire.pages.products.election.home')
            ->with(key: [

            ])
            ->layoutData(data: [
                'seoData' => new SEOData(
                    title: __('pages/products/election/home.seo.title'),
                    description: __('pages/products/election/home.seo.description'),
                ),
            ]);
    }
}
