<?php

namespace Database\Factories;

use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    public function definition(): array
    {
        return [
            'membership_number' => strtoupper($this->faker->bothify(string: '??#####')),
            'name' => $this->faker->name(),
            'email' => null,
            'phone' => null,
            'weightage' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function withName(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => $this->faker->name(),
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

    public function withWeightage(?string $phone = null): static
    {
        return $this->state(function (array $attributes) use ($phone) {
            return [
                'weightage' => $phone ?: $this->faker->randomFloat(2, 0.01, 100),
            ];
        });
    }
}
