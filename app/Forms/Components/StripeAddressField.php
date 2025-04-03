<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Support\Arr;

class StripeAddressField extends Field
{
    protected string $view = 'forms.components.stripe-address-field';

    protected ?string $stripeKey = null;

    protected string $displayName = 'full';

    public function displayNameOrganization(): self
    {
        $this->displayName = 'organization';

        return $this;
    }

    public function displayNameFull(): self
    {
        $this->displayName = 'full';

        return $this;
    }

    public function displayNameSplit(): self
    {
        $this->displayName = 'split';

        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getStripeKey(): ?string
    {
        return $this->stripeKey ?? config('services.stripe.key');
    }

    public function getAppearance(): array
    {
        return [
            'theme' => 'stripe',
        ];
    }

    public function getOptions(): array
    {
        return [
            'mode' => 'billing',
            'defaultValues' => (object) Arr::wrap($this->getState()),
            'display' => [
                'name' => $this->getDisplayName(),
            ],
            'fields' => [
                'phone' => 'always',
            ],
            'validation' => [
                'phone' => [
                    'required' => 'always',
                ],
            ],
        ];
    }
}
