<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wiki_categories', function (Blueprint $table) {
            $table->id();

            $table->string(column: 'name');
            $table->string(column: 'slug')->nullable()->unique();
            $table->text(column: 'summary')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('wiki_pages', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('published_at')
                ->constrained('wiki_categories')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::create('wiki_tags', function (Blueprint $table) {
            $table->id();

            $table->string(column: 'name');
            $table->string(column: 'slug')->nullable()->unique();
            $table->text(column: 'summary')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('wiki_page_wiki_tag', function (Blueprint $table) {
            $table->foreignId('wiki_page_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignId('wiki_tag_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wiki_page_wiki_tag');
        Schema::dropIfExists('wiki_tags');

        Schema::table('wiki_pages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });

        Schema::dropIfExists('wiki_categories');
    }
};
