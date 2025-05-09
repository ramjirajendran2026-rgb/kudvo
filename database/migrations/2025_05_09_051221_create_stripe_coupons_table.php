<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_coupons', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name')->nullable();
            $table->double('percent_off')->nullable();
            $table->integer('amount_off')->nullable();
            $table->char('currency', 3)->nullable();
            $table->json('currency_options')->nullable();
            $table->string('duration')->nullable();
            $table->boolean('valid');
            $table->boolean('livemode');
            $table->integer('max_redemptions')->nullable();
            $table->integer('times_redeemed');
            $table->json('metadata')->nullable();
            $table->timestamp('redeem_by')->nullable();
            $table->timestamp('created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_coupons');
    }
};
