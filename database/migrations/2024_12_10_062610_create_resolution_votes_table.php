<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'resolution_votes',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'response');

                $table->foreignId(column: 'participant_id')
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId(column: 'resolution_id')
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();

                $table->index(['resolution_id', 'response']);
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'resolution_votes');
    }
};
