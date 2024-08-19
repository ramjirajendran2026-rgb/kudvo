<?php

namespace Database\Factories;

use App\Models\WikiCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class WikiCategoryFactory extends Factory
{
    protected $model = WikiCategory::class;

    public function definition(): array
    {
        $name = Str::of($this->faker->unique()->word());

        return [
            'name' => $name->title(),
            'slug' => $name->slug(),
            'summary' => $this->faker->paragraph(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
