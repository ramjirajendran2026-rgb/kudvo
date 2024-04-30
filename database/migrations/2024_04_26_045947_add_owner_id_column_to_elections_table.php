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
                $table->foreignId(column: 'owner_id')->nullable()->after(column: 'stripe_invoice_data')
                    ->constrained(table: 'users')->cascadeOnUpdate()->nullOnDelete();
            },
        );
    }

    public function down(): void
    {
        Schema::table(
            table: 'elections',
            callback: function (Blueprint $table): void {
                $table->dropConstrainedForeignId(column: 'owner_id');
            },
        );
    }
};
