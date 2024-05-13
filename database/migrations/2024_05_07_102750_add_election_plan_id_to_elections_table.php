<?php

use App\Models\ElectionPlan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table(
            table: 'elections',
            callback: function (Blueprint $table): void {
                $table->after(
                    column: 'owner_id',
                    callback: function (Blueprint $table) {
                        $table->foreignId(column: 'plan_id')->nullable()
                            ->constrained(table: 'election_plans')->cascadeOnUpdate()->nullOnDelete();
                    },
                );
            },
        );
    }

    public function down(): void
    {
        Schema::table(
            table: 'elections', callback:
            function (Blueprint $table): void {
                $table->dropConstrainedForeignId(column: 'plan_id');
            },
        );
    }
};
