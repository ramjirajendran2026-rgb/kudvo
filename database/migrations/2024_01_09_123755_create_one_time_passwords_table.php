<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'one_time_passwords',
            callback: function (Blueprint $table) {
                $table->id();
                $table->uuid()->unique();

                $table->longText(column: 'code');
                $table->string(column: 'purpose')->nullable();
                $table->string(column: 'email')->nullable();
                $table->string(column: 'phone')->nullable();

                $table->unsignedInteger(column: 'total_sent')->default(value: 0);
                $table->unsignedInteger(column: 'total_attempt')->default(value: 0);

                $table->timestamp(column: 'expires_at');
                $table->timestamp(column: 'sent_at')->nullable();
                $table->timestamp(column: 'verified_at')->nullable();

                $table->nullableMorphs(name: 'relatable');

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'one_time_passwords');
    }
};
