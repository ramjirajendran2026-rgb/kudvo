<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('answers');
        Schema::dropIfExists('entries');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('surveys');

        Schema::enableForeignKeyConstraints();

        DB::table('migrations')
            ->where('migration', 'like', '%_create_surveys_table')
            ->orWhere('migration', 'like', '%_create_questions_table')
            ->orWhere('migration', 'like', '%_create_entries_table')
            ->orWhere('migration', 'like', '%_create_answers_table')
            ->orWhere('migration', 'like', '%_create_sections_table')
            ->orWhere('migration', 'like', '%_add_sort_column_to_questions_table')
            ->orWhere('migration', 'like', '%_change_value_field_type_in_answers_table')
            ->delete();
    }
};
