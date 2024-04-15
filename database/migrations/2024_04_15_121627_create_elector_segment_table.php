<?php

use App\Models\Elector;
use App\Models\Segment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            table: 'elector_segment',
            callback: function (Blueprint $table): void {
                $table->foreignIdFor(model: Elector::class);
                $table->foreignIdFor(model: Segment::class);

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'elector_segment');
    }
};
