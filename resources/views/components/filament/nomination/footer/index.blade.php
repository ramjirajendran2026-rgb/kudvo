@php
    use App\Facades\Kudvo;
@endphp

<footer class="fi-footer">
    <div>
        <div>
            {{ Kudvo::isBoothDevice() ? 'Booth Voting by' : 'Powered by' }}
            <x-filament::link :href="filament()->getHomeUrl()">
                {{ config('app.name') }}
            </x-filament::link>
        </div>
    </div>
</footer>
