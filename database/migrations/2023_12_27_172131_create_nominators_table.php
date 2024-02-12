<?php

use App\Models\Elector;
use App\Models\Nominee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'nominators',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'membership_number');
                $table->string(column: 'title')->nullable();
                $table->string(column: 'first_name')->nullable();
                $table->string(column: 'last_name')->nullable();
                $table->string(column: 'full_name')
                    ->virtualAs(
                        expression: 'CONCAT_WS(" ", NULLIF(first_name, ""), NULLIF(last_name, ""))'
                    );
                $table->string(column: 'email')->nullable();
                $table->string(column: 'phone')->nullable();

                $table->string(column: 'status');
                $table->timestamp(column: 'decided_at')->nullable();

                $table->foreignIdFor(model: Nominee::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(model: Elector::class)->nullable()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'nominators');
    }
};
