<?php

use App\Models\Election;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            table: 'election_user_invitations',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'email');
                $table->ulid(column: 'token')->unique();

                $table->string(column: 'designation')->nullable();
                $table->json(column: 'permissions')->nullable();

                $table->timestamp(column: 'accepted_at')->nullable();

                $table->foreignIdFor(model: Election::class)
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignIdFor(model: User::class)->nullable()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->foreignId(column: 'invitor_id')->nullable()
                    ->constrained(table: 'users')->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
                $table->softDeletes();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'election_user_invitations');
    }
};
