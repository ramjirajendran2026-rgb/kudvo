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

                $table->text(column: 'title');

                $table->string(column: 'timezone')->nullable();
                $table->timestamp(column: 'voting_starts_at')->nullable();
                $table->timestamp(column: 'voting_ends_at')->nullable();

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
