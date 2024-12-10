<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'resolutions',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->longText(column: 'overview');

                $table->foreignId(column: 'meeting_id')
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'resolutions');
    }
};
