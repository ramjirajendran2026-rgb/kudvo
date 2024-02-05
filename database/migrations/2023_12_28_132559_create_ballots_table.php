<?php

use App\Models\AuthSession;
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

                $table->string(column: 'type');
                $table->ipAddress()->nullable();

                $table->timestamp(column: 'voted_at')->nullable();

                $table->boolean(column: 'mock')->default(value: false);

                $table->foreignIdFor(model: Elector::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(model: AuthSession::class)->nullable()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'ballots');
    }
};
