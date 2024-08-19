<?php

namespace Database\Seeders;

use App\Models\WikiCategory;
use App\Models\WikiPage;
use App\Models\WikiTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DummyWikiSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('wiki_page_wiki_tag')->truncate();

        WikiPage::truncate();
        WikiTag::truncate();
        WikiCategory::truncate();

        Schema::enableForeignKeyConstraints();

        $categories = WikiCategory::factory(5)->create();
        $tags = WikiTag::factory(14)->create();

        WikiPage::factory(65)
            ->recycle($categories)
            ->create()
            ->each(function (WikiPage $page) use ($tags) {
                $page->tags()->attach($tags->random(rand(1, 5))->pluck('id'));
            });
    }
}
