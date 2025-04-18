@php
    use Filament\Support\Facades\FilamentAsset;
    use Filament\Support\Facades\FilamentView;

    $formattedId = str_replace('.', '-', $getId());
@endphp

<div
    @if (FilamentView::hasSpaMode())
        {{-- format-ignore-start --}}x-load="visible || event (ax-modal-opened)" {{-- format-ignore-end --}}
    @else
        x-load
    @endif
    x-load-js="[@js(FilamentAsset::getScriptSrc('stripe-address-form-component'))]"
    x-data="{
        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }},
        stripe: null,
        elements: null,
        addressElement: null,
        appearance: @js($getAppearance()),
        stripeKey: @js($getStripeKey()),
        options: @js($getOptions()),
        init() {
            this.stripe = Stripe(this.stripeKey)
            const appearance = this.appearance
            this.elements = this.stripe.elements({ appearance })
            this.addressElement = this.elements.create('address', this.options)
            this.addressElement.on('change', (e) => {
                this.state = e.value
            })
            this.addressElement.mount('#{{ $formattedId }}')
        },
    }"
>
    <div id="{{ $formattedId }}"></div>
</div>

@pushonce('scripts')
    <script src="https://js.stripe.com/v3/"></script>
@endpushonce
