<?php

use App\Models\Ballot;
use App\Models\Position;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'votes',
            callback: function (Blueprint $table): void {
                $table->uuid(column: 'id')->primary();

                $table->longText(column: 'content');

                $table->foreignIdFor(model: Ballot::class)->nullable()
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(model: Position::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'votes');
    }
};
