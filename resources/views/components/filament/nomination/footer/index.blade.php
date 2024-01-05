<footer class="fi-footer">
    <div>
        <div>
            Powered by
            <x-filament::link
                :href="filament()->getHomeUrl()"
            >
                {{ config('app.name') }}
            </x-filament::link>
        </div>
    </div>
</footer>
