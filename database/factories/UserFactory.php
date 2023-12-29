<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->e164PhoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make(value: 'password'),
            'remember_token' => Str::random(length: 10),
        ];
    }

    public function unverifiedPhone(): static
    {
        return $this->state(fn (array $attributes): array => [
            'phone_verified_at' => null,
        ]);
    }

    public function unverifiedEmail(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }
}
