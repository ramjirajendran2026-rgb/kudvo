<?php

namespace Database\Factories;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Organisation>
 */
class OrganisationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->lexify(string: '????????'),
            'name' => $this->faker->company(),
            'country' => $this->faker->countryCode(),
            'timezone' => $this->faker->timezone(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function withoutCode(): static
    {
        return $this->state(fn (array $attributes): array => [
            'code' => null,
        ]);
    }
}
