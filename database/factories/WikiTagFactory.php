<?php

namespace Database\Factories;

use App\Models\WikiTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class WikiTagFactory extends Factory
{
    protected $model = WikiTag::class;

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
