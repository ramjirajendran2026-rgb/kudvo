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
                    column: 'cancelled_at',
                    callback: function (Blueprint $table): void {
                        $table->timestamp(column: 'paid_at')->nullable();
                        $table->string(column: 'invoice_status')->nullable();

                        $table->string(column: 'stripe_invoice_id')->collation(collation: 'utf8_bin')->nullable()
                            ->unique();
                        $table->json(column: 'stripe_invoice_data')->nullable();
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
                $table->dropColumn(columns: ['invoice_status', 'stripe_invoice_id', 'stripe_invoice_data']);
            },
        );
    }
};
