<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(table: 'ballots', callback: function (Blueprint $table): void {
            $table->foreignId(column: 'booth_id')->nullable()
                ->after(column: 'mock')
                ->constrained(table: 'election_booth_tokens')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table(table: 'votes', callback: function (Blueprint $table): void {
            $table->foreignId(column: 'booth_id')->nullable()
                ->after(column: 'ballot_id')
                ->constrained(table: 'election_booth_tokens')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table(table: 'ballots', callback: function (Blueprint $table) {
            $table->dropConstrainedForeignId(column: 'booth_id');
        });

        Schema::table(table: 'votes', callback: function (Blueprint $table) {
            $table->dropConstrainedForeignId(column: 'booth_id');
        });
    }
};
