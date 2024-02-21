<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table(
            table: 'elections',
            callback: function (Blueprint $table): void {
                $table->after(
                    column: 'ends_at',
                    callback: function (Blueprint $table): void {
                        $table->timestamp(column: 'booth_starts_at')->nullable();
                        $table->timestamp(column: 'booth_ends_at')->nullable();
                    },
                );
            },
        );
    }

    public function down(): void
    {
        Schema::table(
            table: 'elections',
            callback: function (Blueprint $table): void {
                $table->dropColumn(columns: ['booth_starts_at', 'booth_ends_at']);
            },
        );
    }
};
