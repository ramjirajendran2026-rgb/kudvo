<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'election_import', callback: function (Blueprint $table): void {
            $table->foreignId(column: 'election_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId(column: 'import_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->json(column: 'options')->nullable();
            $table->json(column: 'column_map')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'election_import');
    }
};
