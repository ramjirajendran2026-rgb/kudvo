@php
    $latestPage = $this->latestPage;
@endphp

<main class="pg-wiki pg-index h-full w-full">
    @if($latestPage)
        <section class="w-full py-8 md:py-16 container">
            <div class="grid items-center gap-8 lg:grid-cols-2">
                <div class="space-y-4">
                    <span class="inline-block rounded-lg bg-blue-800 px-3 py-1 text-sm text-white">
                        {{ $latestPage->category?->name }}
                    </span>
                    <h1 class="text-3xl hover:underline cursor-pointer font-bold tracking-tighter sm:text-4xl md:text-5xl">
                        <a href="{{ route('wiki.show', [$latestPage]) }}">{{ $latestPage->title }}</a>
                    </h1>

                    <p class="md:text-xl/relaxed line-clamp-5">
                        {{ $latestPage->summary }}
                    </p>
                </div>

                @php
                    $img = $latestPage->getFirstMedia('cover')
                        ?->img(extraAttributes: [
                            'alt' => $latestPage->title,
                            'class' => 'aspect-video rounded-lg object-cover w-full'
                        ])
                @endphp

                @if($img)
                    {{ $img }}
                @else
                    <img
                        src="{{ $latestPage->getDefaultCoverUrl() }}"
                        alt="{{ $latestPage->title }}"
                        class="aspect-video rounded-lg object-cover w-full"
                    />
                @endif
            </div>

            <svg class="fixed inset-0 -z-10 object-none size-full" xmlns='http://www.w3.org/2000/svg'>
                <rect fill='#ffffff' height="100%" />
                <defs>
                    <rect stroke='#ffffff' stroke-width='0.3' width='1' height='1' id='s' />
                    <pattern id='a' width='3' height='3' patternUnits='userSpaceOnUse'
                             patternTransform='scale(32.35) translate(-969.09 -726.82)'>
                        <use fill='#fcfcfc' href='#s' y='2' />
                        <use fill='#fcfcfc' href='#s' x='1' y='2' />
                        <use fill='#fafafa' href='#s' x='2' y='2' />
                        <use fill='#fafafa' href='#s' />
                        <use fill='#f7f7f7' href='#s' x='2' />
                        <use fill='#f7f7f7' href='#s' x='1' y='1' />
                    </pattern>
                    <pattern id='b' width='7' height='11' patternUnits='userSpaceOnUse'
                             patternTransform='scale(32.35) translate(-969.09 -726.82)'>
                        <g fill='#f5f5f5'>
                            <use href='#s' />
                            <use href='#s' y='5' />
                            <use href='#s' x='1' y='10' />
                            <use href='#s' x='2' y='1' />
                            <use href='#s' x='2' y='4' />
                            <use href='#s' x='3' y='8' />
                            <use href='#s' x='4' y='3' />
                            <use href='#s' x='4' y='7' />
                            <use href='#s' x='5' y='2' />
                            <use href='#s' x='5' y='6' />
                            <use href='#s' x='6' y='9' />
                        </g>
                    </pattern>
                    <pattern id='h' width='5' height='13' patternUnits='userSpaceOnUse'
                             patternTransform='scale(32.35) translate(-969.09 -726.82)'>
                        <g fill='#f5f5f5'>
                            <use href='#s' y='5' />
                            <use href='#s' y='8' />
                            <use href='#s' x='1' y='1' />
                            <use href='#s' x='1' y='9' />
                            <use href='#s' x='1' y='12' />
                            <use href='#s' x='2' />
                            <use href='#s' x='2' y='4' />
                            <use href='#s' x='3' y='2' />
                            <use href='#s' x='3' y='6' />
                            <use href='#s' x='3' y='11' />
                            <use href='#s' x='4' y='3' />
                            <use href='#s' x='4' y='7' />
                            <use href='#s' x='4' y='10' />
                        </g>
                    </pattern>
                    <pattern id='c' width='17' height='13' patternUnits='userSpaceOnUse'
                             patternTransform='scale(32.35) translate(-969.09 -726.82)'>
                        <g fill='#f2f2f2'>
                            <use href='#s' y='11' />
                            <use href='#s' x='2' y='9' />
                            <use href='#s' x='5' y='12' />
                            <use href='#s' x='9' y='4' />
                            <use href='#s' x='12' y='1' />
                            <use href='#s' x='16' y='6' />
                        </g>
                    </pattern>
                    <pattern id='d' width='19' height='17' patternUnits='userSpaceOnUse'
                             patternTransform='scale(32.35) translate(-969.09 -726.82)'>
                        <g fill='#ffffff'>
                            <use href='#s' y='9' />
                            <use href='#s' x='16' y='5' />
                            <use href='#s' x='14' y='2' />
                            <use href='#s' x='11' y='11' />
                            <use href='#s' x='6' y='14' />
                        </g>
                        <g fill='#efefef'>
                            <use href='#s' x='3' y='13' />
                            <use href='#s' x='9' y='7' />
                            <use href='#s' x='13' y='10' />
                            <use href='#s' x='15' y='4' />
                            <use href='#s' x='18' y='1' />
                        </g>
                    </pattern>
                    <pattern id='e' width='47' height='53' patternUnits='userSpaceOnUse'
                             patternTransform='scale(32.35) translate(-969.09 -726.82)'>
                        <g fill='#FFFFFF'>
                            <use href='#s' x='2' y='5' />
                            <use href='#s' x='16' y='38' />
                            <use href='#s' x='46' y='42' />
                            <use href='#s' x='29' y='20' />
                        </g>
                    </pattern>
                    <pattern id='f' width='59' height='71' patternUnits='userSpaceOnUse'
                             patternTransform='scale(32.35) translate(-969.09 -726.82)'>
                        <g fill='#FFFFFF'>
                            <use href='#s' x='33' y='13' />
                            <use href='#s' x='27' y='54' />
                            <use href='#s' x='55' y='55' />
                        </g>
                    </pattern>
                    <pattern id='g' width='139' height='97' patternUnits='userSpaceOnUse'
                             patternTransform='scale(32.35) translate(-969.09 -726.82)'>
                        <g fill='#FFFFFF'>
                            <use href='#s' x='11' y='8' />
                            <use href='#s' x='51' y='13' />
                            <use href='#s' x='17' y='73' />
                            <use href='#s' x='99' y='57' />
                        </g>
                    </pattern>
                </defs>
                <rect fill='url(#a)' width='100%' height='100%' />
                <rect fill='url(#b)' width='100%' height='100%' />
                <rect fill='url(#h)' width='100%' height='100%' />
                <rect fill='url(#c)' width='100%' height='100%' />
                <rect fill='url(#d)' width='100%' height='100%' />
                <rect fill='url(#e)' width='100%' height='100%' />
                <rect fill='url(#f)' width='100%' height='100%' />
                <rect fill='url(#g)' width='100%' height='100%' />
            </svg>
        </section>
    @endif

    <section class="py-6 container bg-primary-50 md:rounded-t-3xl space-y-6">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-1">
                {{ $this->table }}
            </div>

            <div class="space-y-6 w-full md:max-w-xs">
                @if($this->categories->count())
                    <section
                        class="bg-white shadow-sm ring-1 rounded-xl ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <h3 class="text-lg font-semibold leading-6 text-primary-600 px-6 py-4 border-b flex justify-between items-center">
                            <span>Categories</span>
                            @if($activeCategory)
                                <span x-on:click="$wire.set('activeCategory', null)"
                                      class="text-xs text-gray-900 cursor-pointer hover:underline"
                                      title="Clear category filter"
                                      role="button"
                                      aria-label="Clear category filter"
                                      tabindex="0"
                                >
                                    Clear
                                </span>
                            @endif
                        </h3>
                        <div class="flex flex-col divide-y">
                            @foreach($this->categories as $category)
                                <span
                                    x-on:click="$wire.set('activeCategory', @js($category->slug))"
                                    class="text-md font-medium px-6 py-2 hover:bg-primary-50 last:rounded-b-xl cursor-pointer"
                                    x-bind:class="{
                                    'bg-primary-50 text-primary-600': @js($activeCategory) === @js($category->slug)
                                }"
                                >
                                {{ $category->name }}
                            </span>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if($this->tags->count())
                    <section
                        class="bg-white shadow-sm ring-1 rounded-xl ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <h3 class="text-lg font-semibold leading-6 text-primary-600 dark:text-white px-6 py-4 border-b flex justify-between items-center">
                            <span>Tags</span>
                            @if($activeTag)
                                <span x-on:click="$wire.set('activeTag', null)"
                                      class="text-xs text-gray-900 cursor-pointer hover:underline"
                                      title="Clear tag filter"
                                      role="button"
                                      aria-label="Clear tag filter"
                                      tabindex="0"
                                >
                                Clear
                            </span>
                            @endif
                        </h3>
                        <div class="flex flex-wrap gap-2 py-4 px-6">
                            @foreach($this->tags as $tag)
                                <x-filament::badge
                                    :color="$activeTag == $tag->slug ? 'primary' : 'gray'"
                                    x-on:click="$wire.set('activeTag', {{ \Illuminate\Support\Js::from($tag->slug) }})"
                                    class="cursor-pointer"
                                >
                                    {{ $tag->name }}
                                </x-filament::badge>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </section>
</main>
