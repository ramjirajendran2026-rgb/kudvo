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
                $table->dropColumn(columns: 'web_app_manifest');
            },
        );
    }

    public function down(): void
    {
        Schema::table(
            table: 'elections',
            callback: function (Blueprint $table): void {
                $table->json(column: 'web_app_manifest')->nullable()
                    ->after(column: 'short_code');
            },
        );
    }
};
