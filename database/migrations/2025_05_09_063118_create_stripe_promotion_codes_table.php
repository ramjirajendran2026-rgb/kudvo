<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_promotion_codes', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('code');
            $table->string('coupon_id')->index();
            $table->json('metadata')->nullable();
            $table->boolean('active');
            $table->timestamp('created');
            $table->string('customer_id')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('livemode');
            $table->integer('max_redemptions')->nullable();
            $table->json('restrictions');
            $table->integer('times_redeemed')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_promotion_codes');
    }
};
