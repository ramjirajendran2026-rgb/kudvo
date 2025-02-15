<x-filament-panels::page>
    <div
        class=" bg-gray-50 text-gray-950 dark:bg-gray-950 dark:text-white"
    >
        <div class="mx-auto max-w-screen-lg">
            <div
                class="grid grid-cols-1 items-center justify-evenly gap-8 md:grid-cols-2 lg:grid-cols-2"
            >
                @foreach ($this->products as $index => $product)
                    <a
                        href="{{ $product['url'] }}"
                        x-data="{ show: false }"
                        x-init="setTimeout(() => (show = true), {{ $index * 100 }})"
                        x-show="show"
                        x-transition
                        class="group relative h-full"
                    >
                        <!-- Animated Gradient Glow Effect -->
                        <div
                            class="transition-color absolute -inset-0.5 rounded-2xl bg-gradient-to-r from-primary-500 to-primary-500 opacity-0 blur duration-300 ease-in-out group-hover:opacity-20"
                        ></div>

                        <!-- Card Container -->
                        <div
                            class="transition-color relative flex h-full flex-col items-stretch justify-between rounded-2xl border border-gray-300 bg-white p-6 text-gray-900 duration-300 ease-in-out dark:border-zinc-800 dark:bg-gray-900 dark:text-gray-100"
                        >
                            <!-- Card Header -->
                            <div class="mb-2 flex items-center space-x-4">
                                <div
                                    class="transition-color rounded-xl border border-primary-300 p-3 duration-300 ease-in-out group-hover:bg-gradient-to-br group-hover:from-primary-500/90 group-hover:to-primary-500/90 dark:border-primary-500 dark:group-hover:bg-gradient-to-br dark:group-hover:from-primary-500/90 dark:group-hover:to-primary-500/90"
                                >
                                    <x-filament::icon
                                        icon="{{ $product['icon'] }}"
                                        class="h-6 w-6 text-primary-500 group-hover:text-white"
                                    />
                                </div>
                                <h3
                                    class="flex items-center justify-center gap-1 bg-gradient-to-r from-primary-500 to-primary-500 bg-clip-text text-2xl font-bold text-transparent"
                                >
                                    {{ $product['title'] }}
                                    @if (! empty($product['badge']))
                                        <x-filament::badge
                                            color="success"
                                            size="xs"
                                            class="size-fit px-2 py-1"
                                        >
                                            {{ $product['badge'] }}
                                        </x-filament::badge>
                                    @endif
                                </h3>
                            </div>

                            <!-- Product Description -->
                            <p class="mb-3 text-gray-700 dark:text-zinc-400">
                                {{ $product['description'] }}
                            </p>
                            <!-- Action Button -->
                            <div
                                class="flex w-full items-center justify-between"
                            >
                                <x-filament::button
                                    outlined
                                    size="lg"
                                    class="transition-color w-full text-primary-500 duration-300 ease-in-out group-hover:bg-gradient-to-br group-hover:from-primary-500/90 group-hover:to-primary-500/90 group-hover:text-white dark:group-hover:bg-gradient-to-br dark:group-hover:from-primary-500/90 dark:group-hover:to-primary-500/90"
                                    color="primary"
                                    tag="span"
                                >
                                    Setup New {{ $product['title'] }}
                                </x-filament::button>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
