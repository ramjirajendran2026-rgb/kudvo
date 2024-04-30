<?php

use App\Models\Election;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('election_user', function (Blueprint $table) {
            $table->string(column: 'designation')->nullable();
            $table->json(column: 'permissions')->nullable();

            $table->foreignIdFor(model: Election::class)
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(model: User::class)
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_user');
    }
};
