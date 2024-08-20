@use(RalphJSmit\Laravel\SEO\Support\SEOData)

@php
    $seoData = new SEOData(
        title: '404 - Page Not Found',
        enableTitleSuffix: false,
        robots: 'noindex, nofollow'
    );
@endphp

<x-layouts.app :seo-data="$seoData">
    <main class="flex flex-1 items-center justify-center">
        <div class="space-y-6 text-center">
            <h1
                class="text-xl font-semibold text-primary-600 sm:text-2xl md:text-4xl"
            >
                404 - Page Not Found
            </h1>
            <p class="text-base">
                Sorry, the page you are looking for does not exist.
            </p>
        </div>
    </main>
</x-layouts.app>
