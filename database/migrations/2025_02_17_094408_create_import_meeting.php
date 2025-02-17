<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_meeting', function (Blueprint $table) {
            $table->foreignId('import_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('meeting_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->json('options')->nullable();
            $table->json('column_map')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_meeting');
    }
};
