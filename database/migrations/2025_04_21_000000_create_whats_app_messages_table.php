<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'whats_app_messages',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'purpose')->nullable();
                $table->string(column: 'phone');
                $table->string(column: 'status')->nullable();
                $table->text(column: 'notes')->nullable();
                $table->string(column: 'message_id')->nullable();
                $table->string(column: 'message_status')->nullable();
                $table->string(column: 'message_type')->nullable();
                $table->longText(column: 'message_meta')->nullable();

                $table->nullableMorphs(name: 'whatsappable');

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'whats_app_messages');
    }
};
