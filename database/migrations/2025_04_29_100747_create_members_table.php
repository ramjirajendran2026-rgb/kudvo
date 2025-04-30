<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string(column: 'full_name')
                ->virtualAs(
                    expression: 'CONCAT_WS(" ", NULLIF(first_name, ""), NULLIF(last_name, ""))'
                );
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('membership_number');
            $table->string('membership_type')->nullable();
            $table->date('membership_end_date')->nullable();

            $table->boolean('is_active')->default(true);

            $table->json('additional_data')->nullable();

            $table->text('password')->nullable();
            $table->rememberToken();

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();

            $table->foreignId('organisation_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['organisation_id', 'membership_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
