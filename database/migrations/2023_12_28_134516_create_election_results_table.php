<?php

use App\Models\Election;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'election_results',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->unsignedInteger(column: 'total_votes')->default(value: 0);
                $table->unsignedInteger(column: 'processed_votes')->default(value: 0);
                $table->timestamp(column: 'completed_at')->nullable();
                $table->longText(column: 'meta')->nullable();

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'election_results');
    }
};
