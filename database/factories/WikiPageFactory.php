<?php

namespace Database\Factories;

use App\Models\WikiCategory;
use App\Models\WikiPage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class WikiPageFactory extends Factory
{
    protected $model = WikiPage::class;

    public function definition(): array
    {
        $title = Str::of($this->faker->unique()->sentence());

        return [
            'title' => $title->title(),
            'slug' => $title->slug(),
            'summary' => $this->faker->paragraph(),
            'content' => collect($this->faker->paragraphs())
                ->map(fn ($p) => "<p>$p</p>")
                ->join(''),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'category_id' => WikiCategory::factory(),
        ];
    }
}
