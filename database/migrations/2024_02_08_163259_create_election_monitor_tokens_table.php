<?php

use App\Models\Election;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('election_monitor_tokens', function (Blueprint $table) {
            $table->id();

            $table->uuid(column: 'key')->unique();
            $table->timestamp(column: 'activated_at')->nullable();
            $table->ipAddress()->nullable();
            $table->text(column: 'user_agent')->nullable();

            $table->foreignIdFor(model: Election::class)
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_monitor_tokens');
    }
};
