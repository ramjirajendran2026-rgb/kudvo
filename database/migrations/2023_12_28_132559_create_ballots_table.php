<?php

use App\Models\Elector;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'ballots',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->ipAddress()->nullable();

                $table->timestamp(column: 'voted_at')->nullable();

                $table->foreignIdFor(model: Elector::class)
                    ->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'ballots');
    }
};
