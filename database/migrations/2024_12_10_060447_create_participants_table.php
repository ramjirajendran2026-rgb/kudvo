<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'participants',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'membership_number')->nullable();
                $table->string(column: 'name')->nullable();
                $table->string(column: 'email')->nullable();
                $table->string(column: 'phone')->nullable();

                $table->timestamp(column: 'voted_at')->nullable();

                $table->ulid('key')->unique();
                $table->string('short_key')->unique();

                $table->foreignId(column: 'meeting_id')
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();

                $table->unique(columns: ['membership_number', 'meeting_id']);
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'participants');
    }
};
