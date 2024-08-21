<?php

namespace App\Actions;

use App\Models\WikiPage;
use Illuminate\Database\Eloquent\Collection;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap
{
    public static function execute(): void
    {
        $sitemap = Sitemap::create();

        $localizedRouteNames = [
            'home',

            'products.election.home',
            'products.election.how-it-works',

            'products.phygital.home',
        ];

        foreach ($localizedRouteNames as $name) {
            $url = Url::create(LaravelLocalization::getLocalizedURL('en', route($name)));

            foreach (LaravelLocalization::getSupportedLanguagesKeys() as $locale) {
                if ($locale === 'en') {
                    continue;
                }
                $url->addAlternate(LaravelLocalization::getLocalizedURL($locale, route($name)), $locale);
            }

            $sitemap->add($url);
        }

        $sitemap->add(route('privacy-policy'));

        $sitemap->add(route('wiki.index'));
        WikiPage::query()
            ->chunkById(50, function (Collection $pages) use ($sitemap) {
                $sitemap->add($pages);
            });

        $sitemap->writeToFile(public_path('sitemap.xml'));
    }
}
