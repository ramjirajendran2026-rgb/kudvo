<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'users',
            callback:  function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'name')->nullable();
                $table->string(column: 'phone')->nullable()->unique();
                $table->string(column: 'email')->nullable()->unique();
                $table->string(column: 'password')->nullable();
                $table->rememberToken();

                $table->timestamp(column: 'phone_verified_at')->nullable();
                $table->timestamp(column: 'email_verified_at')->nullable();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'users');
    }
};
