<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_prices', function (Blueprint $table) {
            $table->id();
            $table->char('country', 2);
            $table->char('currency', 3);
            $table->integer('actual_price');
            $table->integer('margin');
            $table->integer('price')->virtualAs('actual_price + margin');
            $table->string('channel');
            $table->timestamps();

            $table->unique(['country', 'currency', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_prices');
    }
};
