<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();

            $table->ipAddress()->nullable();
            $table->text('user_agent')->nullable();

            $table->integer('sort')->default(1);

            $table->foreignId('survey_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()
                ->constrained()->cascadeOnUpdate()->nullOnDelete();

            $table->timestamps();
        });
    }
};
