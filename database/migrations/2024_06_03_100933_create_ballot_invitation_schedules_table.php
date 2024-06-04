<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'ballot_link_blasts',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->timestamp(column: 'scheduled_at');
                $table->timestamp(column: 'initiated_at')->nullable();
                $table->timestamp(column: 'completed_at')->nullable();
                $table->timestamp(column: 'cancelled_at')->nullable();

                $table->integer(column: 'total_electors')->nullable();
                $table->integer(column: 'processed_electors')->nullable();

                $table->boolean(column: 'is_reminder')->default(value: false);

                $table->uuid(column: 'job_batch_id')->nullable();

                $table->foreignId(column: 'election_id')
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'ballot_link_blasts');
    }
};
