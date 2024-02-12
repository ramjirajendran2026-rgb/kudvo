<?php

use App\Models\Elector;
use App\Models\Position;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'candidates',
            callback: function (Blueprint $table): void {
                $table->id();
                $table->uuid()->unique();

                $table->string(column: 'membership_number')->nullable();
                $table->string(column: 'title')->nullable();
                $table->string(column: 'first_name')->nullable();
                $table->string(column: 'last_name')->nullable();
                $table->string(column: 'full_name')
                    ->virtualAs(
                        expression: 'CONCAT_WS(" ", NULLIF(first_name, ""), NULLIF(last_name, ""))'
                    );
                $table->string(column: 'email')->nullable();
                $table->string(column: 'phone')->nullable();

                $table->integer(column: 'sort')->nullable();

                $table->foreignIdFor(model: Position::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(model: Elector::class)->nullable()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'candidates');
    }
};
