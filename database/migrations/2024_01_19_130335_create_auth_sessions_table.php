<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
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

                $table->morphs(name: 'authenticatable');

                $table->timestamps();
                $table->softDeletes();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'browser_sessions');
    }
};
