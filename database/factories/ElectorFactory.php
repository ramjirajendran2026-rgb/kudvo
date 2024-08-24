<?php

namespace Database\Factories;

use App\Models\Elector;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ElectorFactory extends Factory
{
    protected $model = Elector::class;

    public function definition(): array
    {
        return [
            'membership_number' => strtoupper($this->faker->bothify(string: '??#####')),
            'title' => null,
            'first_name' => null,
            'last_name' => null,
            'email' => null,
            'phone' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function withName(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => $this->faker->title(),
                'first_name' => $this->faker->firstName(),
                'last_name' => $this->faker->lastName(),
            ];
        });
    }

    public function withEmail(?string $email = null): static
    {
        return $this->state(function (array $attributes) use ($email) {
            return [
                'email' => $email ?: $this->faker->safeEmail(),
            ];
        });
    }

    public function withPhone(?string $phone = null): static
    {
        return $this->state(function (array $attributes) use ($phone) {
            return [
                'phone' => $phone ?: $this->faker->e164PhoneNumber(),
            ];
        });
    }
}
