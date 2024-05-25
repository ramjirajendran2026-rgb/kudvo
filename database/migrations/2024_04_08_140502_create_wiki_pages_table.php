<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'wiki_pages',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'title');
                $table->string(column: 'slug')->nullable()->unique();
                $table->text(column: 'summary')->nullable();
                $table->longText(column: 'content')->nullable();
                $table->timestamp(column: 'published_at')->nullable();

                $table->timestamps();
                $table->softDeletes();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'wiki_pages');
    }
};
