<?php

use App\Models\Position;
use App\Models\Segment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

        Schema::create(
            table: 'position_segment',
            callback: function (Blueprint $table): void {
                $table->foreignIdFor(model: Position::class);
                $table->foreignIdFor(model: Segment::class);

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'position_segment');
    }
};
