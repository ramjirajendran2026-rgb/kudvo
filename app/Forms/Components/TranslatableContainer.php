<?php

namespace App\Forms\Components;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Mvenghaus\FilamentPluginTranslatableInline\Forms\Components\TranslatableContainer as BaseComponent;

class TranslatableContainer extends BaseComponent
{
    protected array|Closure|null $translatableLocales = null;

    public function translatableLocales(array|Closure|null $locales): static
    {
        $this->translatableLocales = $locales;

        return $this;
    }

    public function getTranslatableLocales(): Collection
    {
        if (is_null($this->translatableLocales)) {
            return parent::getTranslatableLocales();
        }

        return collect(value: $this->evaluate($this->translatableLocales) ?? [App::getLocale()]);
    }
}
