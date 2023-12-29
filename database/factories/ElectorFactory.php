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
            'uuid' => $this->faker->uuid(),
            'membership_number' => $this->faker->bothify(string: '??#####'),
            'title' => $this->faker->title(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->e164PhoneNumber(),
            'groups' => $this->faker->words(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
