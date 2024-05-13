@php
    use function Filament\Support\prepare_inherited_attributes;
    $id = $getId();
    $isConcealed = $isConcealed();
    $isDisabled = $isDisabled();
    $isNumeric = $isNumeric();
    $isPrefixInline = $isPrefixInline();
    $isSuffixInline = $isSuffixInline();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixLabel = $getPrefixLabel();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixLabel = $getSuffixLabel();
    $statePath = $getStatePath();
    $length = $getLength();
    $isAutofocused = $isAutofocused();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
    	    state: $wire.$entangle('{{ $getStatePath() }}'),
    	    length: @js($length),
    	    autoFocus: @js($isAutofocused),
    	    readOnly: @js($isReadOnly()),
    	    autoFillOnly: @js($isAutoFillOnly()),
    	    ios: @js($isIos()),
            init: function(){
                if (this.autoFocus){
                    this.$refs[1].focus();
                }

                if ('OTPCredential' in window) {
                    $nextTick(() => {
                        const ac = new AbortController();

                        window.addEventListener('livewire:navigating', e => {
                            console.log('aborting...');

                            ac.abort();
                        });

                        window.navigator.credentials.get({
                            otp: { transport:['sms'] },
                            signal: ac.signal
                        }).then(otp => {
                            const code = otp.code;
                            const inputs = Array.from(Array(this.length));

                            inputs.forEach((element, i) => {
                                this.$refs[(i+1)].focus();
                                this.$refs[(i+1)].value = code[i] || '';
                            });

                            $dispatch('otp-received', {code: code});
                        }).catch(err => {
                            console.log(err);
                        });
                    });
                }
            },

            handleKeydown(e, i) {
                if(this.autoFillOnly && this.ios && e.isTrusted) {
                    e.preventDefault();
                    return false;
                }
            },

            handleInput(e, i) {
                const input = e.target;
                if(input.value.length > 1){
                    input.value = input.value.substring(0, 1);
                }

                this.state = Array.from(Array(this.length), (element, i) => {
                    const el = this.$refs[(i + 1)];
                    return el.value ? el.value : '';
                }).join('');


                if (i < this.length) {
                    this.$refs[i+1].focus();
                    this.$refs[i+1].select();
                }
                if(i == this.length){
                    @this.set('{{ $getStatePath() }}', this.state)
                }
            },

            handlePaste(e) {
                if(this.autoFillOnly) {
                    e.preventDefault();

                    return false;
                }

                const paste = e.clipboardData.getData('text');
                const inputs = Array.from(Array(this.length));

                inputs.forEach((element, i) => {
                    this.$refs[(i+1)].focus();
                    this.$refs[(i+1)].value = paste[i] || '';
                });
            },

            handleBackspace(e) {
                const ref = e.target.getAttribute('x-ref');
                e.target.value = '';
                const previous = ref - 1;
                this.$refs[previous] && this.$refs[previous].focus();
                this.$refs[previous] && this.$refs[previous].select();
                e.preventDefault();
            },
        }"
    >
        <div class="flex justify-between gap-4 md:gap-6">
            @foreach (range(1, $length) as $column)
                <x-filament::input.wrapper
                    :disabled="$isDisabled"
                    :inline-prefix="$isPrefixInline"
                    :inline-suffix="$isSuffixInline"
                    :prefix="$prefixLabel"
                    :prefix-actions="$prefixActions"
                    :prefix-icon="$prefixIcon"
                    :prefix-icon-color="$getPrefixIconColor()"
                    :suffix="$suffixLabel"
                    :suffix-actions="$suffixActions"
                    :suffix-icon="$suffixIcon"
                    :suffix-icon-color="$getSuffixIconColor()"
                    :valid="! $errors->has($statePath)"
                    :attributes="
                        \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                        ->class(['overflow-hidden'])
                    "
                >
                    <input
                        maxlength="1"
                        type="{{ $isNumeric ? 'number' : 'text' }}"
                        required
                        {!! $isDisabled ? 'disabled' : 'wire:loading.attr="disabled"' !!}
                        class="fi-input fi-otp-input block w-full border-none bg-white/0 py-1.5 pe-3 ps-3 text-center text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)]"
                        x-ref="{{ $column }}"
                        x-bind:readonly="readOnly || (autoFillOnly && ! ios)"
                        x-on:keydown="handleKeydown($event, {{ $column }})"
                        x-on:input="handleInput($event, {{ $column }})"
                        x-on:paste="handlePaste($event)"
                        x-on:keydown.backspace="handleBackspace($event)"
                    />
                </x-filament::input.wrapper>
            @endforeach
        </div>
    </div>
</x-dynamic-component>

<style>
    input.fi-otp-input[type='number'] {
        -webkit-appearance: textfield;
        -moz-appearance: textfield;
        appearance: textfield;
        overflow: visible;
    }

    input.fi-otp-input[type='number']::-webkit-inner-spin-button,
    input.fi-otp-input[type='number']::-webkit-outer-spin-button {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0;
    }
</style>
