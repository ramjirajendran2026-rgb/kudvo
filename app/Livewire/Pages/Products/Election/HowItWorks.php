<?php

namespace App\Livewire\Pages\Products\Election;

use Livewire\Component;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class HowItWorks extends Component
{
    public function render()
    {
        return view('livewire.pages.products.election.how-it-works')
            ->layoutData(data: [
                'seoData' => new SEOData(
                    title: __('pages/products/election/how-it-works.seo.title'),
                    description: __('pages/products/election/how-it-works.seo.description'),
                ),
            ]);
    }
}
