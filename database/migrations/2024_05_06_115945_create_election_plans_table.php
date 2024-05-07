<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            table: 'election_plans',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'name');
                $table->text(column: 'description')->nullable();
                $table->char(column: 'currency', length: 3);
                $table->integer(column: 'base_fee')->default(value: 0);
                $table->integer(column: 'elector_fee')->default(value: 0);

                $table->json(column: 'features')->nullable();

                $table->integer(column: 'sort')->default(value: 0);

                $table->softDeletes();
                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'election_plans');
    }
};
