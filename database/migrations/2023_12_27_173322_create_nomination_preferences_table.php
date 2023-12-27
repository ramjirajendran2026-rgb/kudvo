<?php

use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'nomination_preferences',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->boolean(column: 'self_nomination')->default(value: false);
                $table->unsignedInteger(column: 'nominator_threshold')->default(value: 0);

                $table->foreignIdFor(model: Event::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
                $table->softDeletes();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'nomination_preferences');
    }
};
