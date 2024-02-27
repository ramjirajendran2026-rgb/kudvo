<?php

use App\Models\CandidateGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table(
            table: 'candidates',
            callback: function (Blueprint $table): void {
                $table->after(
                    column: 'position_id',
                    callback: function (Blueprint $table) {
                        $table->foreignIdFor(model: CandidateGroup::class)->nullable()
                            ->constrained()->cascadeOnUpdate()->nullOnDelete();
                    },
                );
            },
        );
    }

    public function down(): void
    {
        Schema::table(
            table: 'candidates',
            callback: function (Blueprint $table): void {
                $table->dropColumn(columns: 'election_team_id');
            },
        );
    }
};
