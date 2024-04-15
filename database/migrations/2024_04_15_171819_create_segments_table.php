<?php

use App\Models\Election;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            table: 'segments',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'name');

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();

                $table->unique(columns: ['election_id', 'name']);
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'segments');
    }
};
