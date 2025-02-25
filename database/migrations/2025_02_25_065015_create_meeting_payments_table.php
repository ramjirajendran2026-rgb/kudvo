<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_payments', function (Blueprint $table) {
            $table->id();

            $table->char('currency', 3);
            $table->integer('amount')->default(0);
            $table->integer('base_fee')->default(0);
            $table->integer('participant_fee')->default(0);
            $table->integer('participant_count')->default(0);

            $table->timestamp('paid_at')->nullable();

            $table->string('stripe_invoice_id')->collation('utf8mb4_bin')->nullable();
            $table->json('stripe_invoice_data')->nullable();

            $table->foreignId('meeting_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }
};
