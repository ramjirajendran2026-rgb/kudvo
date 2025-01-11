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

                $table->string(column: 'code')->unique();
                $table->string(column: 'name');
                $table->text(column: 'description')->nullable();

                $table->string(column: 'timezone')->nullable();
                $table->timestamp(column: 'voting_starts_at')->nullable();
                $table->timestamp(column: 'voting_ends_at')->nullable();
                $table->timestamp(column: 'voting_closed_at')->nullable();

                $table->timestamp(column: 'published_at')->nullable();
                $table->timestamp(column: 'cancelled_at')->nullable();

                $table->ulid(column: 'key')->unique();
                $table->string(column: 'short_key')->unique();

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
