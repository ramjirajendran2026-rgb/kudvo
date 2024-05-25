<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'password_reset_tokens',
            callback: function (Blueprint $table): void {
                $table->string('email')->primary();

                $table->string('token');

                $table->timestamp('created_at')->nullable();
            },
        );

        Schema::create(
            table: 'auth_sessions',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'session_id')->index();
                $table->string(column: 'guard_name');
                $table->ipAddress()->nullable();
                $table->text(column: 'user_agent')->nullable();
                $table->timestamp(column: 'mfa_completed_at')->nullable();
                $table->timestamp(column: 'last_activity_at')->useCurrent();
                $table->timestamp(column: 'expires_at')->nullable();

                $table->morphs(name: 'authenticatable');

                $table->timestamps();
                $table->softDeletes();
            },
        );

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

                $table->timestamp(column: 'expires_at')->nullable();
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

        Schema::dropIfExists(table: 'auth_sessions');

        Schema::dropIfExists(table: 'password_reset_tokens');
    }
};
