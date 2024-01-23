<?php

use App\Models\Organisation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'nominations',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'code')->unique();
                $table->string(column: 'name')->index();
                $table->longText(column: 'description')->nullable();

                $table->json(column: 'preference')->nullable();

                $table->boolean(column: 'self_nomination')->default(value: false);
                $table->unsignedInteger(column: 'nominator_threshold')->default(value: 2);

                $table->string(column: 'timezone')->nullable();
                $table->timestamp(column: 'starts_at')->nullable();
                $table->timestamp(column: 'ends_at')->nullable();
                $table->timestamp(column: 'withdrawal_starts_at')->nullable();
                $table->timestamp(column: 'withdrawal_ends_at')->nullable();

                $table->timestamp(column: 'published_at')->nullable();
                $table->timestamp(column: 'closed_at')->nullable();
                $table->timestamp(column: 'scrutinised_at')->nullable();
                $table->timestamp(column: 'cancelled_at')->nullable();

                $table->foreignIdFor(model: Organisation::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'nominations');
    }
};
