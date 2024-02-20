<?php

use App\Models\Election;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            table: 'election_booth_tokens',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->uuid(column: 'key')->unique();
                $table->timestamp(column: 'activated_at')->nullable();
                $table->ipAddress()->nullable();
                $table->text(column: 'user_agent')->nullable();

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
                $table->softDeletes();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'election_booth_tokens');
    }
};
