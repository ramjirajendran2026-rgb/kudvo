<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();

            $table->text('text')->nullable();
            $table->string('type');
            $table->json('options')->nullable();
            $table->boolean('has_other_option')->default(false);
            $table->boolean('is_required')->default(false);

            $table->integer('sort')->default(1);

            $table->foreignId('survey_id')
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }
};
