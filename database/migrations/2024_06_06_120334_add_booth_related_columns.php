<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            table: 'elections',
            callback: function (Blueprint $table): void {
                $table->json(column: 'booth_preference')->nullable()
                    ->after(column: 'preference');
            },
        );

        Schema::table(
            table: 'election_booth_tokens',
            callback: function (Blueprint $table): void {
                $table->after(column: 'id', callback: function (Blueprint $table): void {
                    $table->string(column: 'name')->nullable();
                    $table->integer(column: 'sort')->default(value: 1);
                });

                $table->foreignId(column: 'current_elector_id')->nullable()
                    ->after(column: 'election_id')
                    ->constrained(table: 'electors')->cascadeOnUpdate()->nullOnDelete();
            },
        );
    }

    public function down(): void
    {
        Schema::table(
            table: 'election_booth_tokens',
            callback: function (Blueprint $table): void {
                $table->dropConstrainedForeignId(column: 'current_elector_id');

                $table->dropColumn(columns: ['name', 'sort']);
            },
        );

        Schema::table(
            table: 'elections',
            callback: function (Blueprint $table): void {
                $table->dropColumn(columns: 'booth_preference');
            },
        );
    }
};
