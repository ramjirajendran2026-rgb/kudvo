<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();

            $table->text('title')->nullable();
            $table->longText('description')->nullable();

            $table->json('settings')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamp('published_at')->nullable();

            $table->foreignId('organisation_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }
};
