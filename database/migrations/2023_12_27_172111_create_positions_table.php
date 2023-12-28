<?php

use App\Models\Election;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'positions',
            callback: function (Blueprint $table): void {
                $table->id();
                $table->uuid()->unique();

                $table->string(column: 'name');
                $table->unsignedInteger(column: 'quota');
                $table->unsignedInteger(column: 'threshold')->nullable();
                $table->json(column: 'elector_groups')->nullable();

                $table->integer(column: 'sort')->nullable();

                $table->morphs(name: 'event');

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'positions');
    }
};
