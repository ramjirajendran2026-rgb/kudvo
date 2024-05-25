<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'sms_messages',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'purpose')->nullable();
                $table->string(column: 'phone');
                $table->string(column: 'status')->nullable();
                $table->text(column: 'notes')->nullable();
                $table->string(column: 'provider')->nullable();
                $table->string(column: 'provider_message_id')->nullable();
                $table->string(column: 'provider_status')->nullable();
                $table->longText(column: 'provider_meta')->nullable();

                $table->nullableMorphs(name: 'smsable');

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'sms_messages');
    }
};
