<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'meetings',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'name');
                $table->text(column: 'description')->nullable();

                $table->timestamp('published_at')->nullable();

                $table->foreignId(column: 'organisation_id')
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'meetings');
    }
};
