<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'organisations',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'code')->unique();
                $table->string(column: 'name')->nullable();
                $table->string(column: 'country')->nullable();
                $table->string(column: 'timezone')->nullable();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'organisations');
    }
};
