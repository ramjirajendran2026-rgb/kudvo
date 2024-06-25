<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(table: 'candidates', callback: function (Blueprint $table): void {
            $table->boolean(column: 'disabled')->default(value: false)
                ->after(column: 'phone');
        });
    }

    public function down(): void
    {
        Schema::table(table: 'candidates', callback: function (Blueprint $table): void {
            $table->dropColumn(columns: 'disabled');
        });
    }
};
