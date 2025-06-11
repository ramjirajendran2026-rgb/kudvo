<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->timestamp('voting_starts_at')->nullable()->after('elector_groups');
            $table->timestamp('voting_ends_at')->nullable()->after('voting_starts_at');
        });

        Schema::create('candidate_fallback_positions', function (Blueprint $table) {
            $table->id();

            $table->integer('sort')->nullable();

            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::table('candidates', function (Blueprint $table) {
            $table->foreignId('primary_candidate_id')->nullable()
                ->after('position_id')
                ->constrained('candidates')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('ballots', function (Blueprint $table) {
            $table->json('position_keys')->nullable()->after('voted_at');
        });
    }

    public function down(): void
    {
        Schema::table('ballots', function (Blueprint $table) {
            $table->dropColumn('position_keys');
        });

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('primary_candidate_id');
        });

        Schema::dropIfExists('candidate_fallback_positions');

        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn(['voting_starts_at', 'voting_ends_at']);
        });
    }
};
