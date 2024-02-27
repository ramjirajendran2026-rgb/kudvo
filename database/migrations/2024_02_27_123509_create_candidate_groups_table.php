<?php

use App\Models\Election;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            table: 'candidate_groups',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'name', length: 100);
                $table->string(column: 'short_name', length: 10);

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();

                $table->unique(columns: ['election_id', 'short_name']);
                $table->unique(columns: ['election_id', 'name']);
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'candidate_groups');
    }
};
