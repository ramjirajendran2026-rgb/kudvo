<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            table: 'election_prices',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->char(column: 'currency', length: 3);
                $table->unsignedInteger(column: 'base_fee')->default(value: 0);
                $table->json(column: 'elector_fee_breakup')->nullable();

                $table->boolean(column: 'enabled')->default(value: true);

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'election_prices');
    }
};
