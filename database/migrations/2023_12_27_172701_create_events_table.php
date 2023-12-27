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
            table: 'events',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'type')->index();
                $table->string(column: 'code')->unique();
                $table->string(column: 'name')->index();
                $table->longText(column: 'description')->nullable();

                $table->string(column: 'timezone')->nullable();
                $table->timestamp(column: 'starts_at')->nullable();
                $table->timestamp(column: 'starts_at')->nullable();

                $table->timestamp(column: 'activated_at')->nullable();
                $table->timestamp(column: 'closed_at')->nullable();
                $table->timestamp(column: 'cancelled_at')->nullable();

                $table->foreignIdFor(model: Organisation::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
                $table->softDeletes();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'events');
    }
};
