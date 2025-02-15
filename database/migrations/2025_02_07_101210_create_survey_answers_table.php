<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();

            $table->json('content')->nullable();

            $table->foreignId('question_id')
                ->constrained('survey_questions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('response_id')
                ->constrained('survey_responses')->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }
};
