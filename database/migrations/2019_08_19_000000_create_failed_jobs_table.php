<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            table: 'failed_jobs',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->uuid()->unique();
                $table->text(column: 'connection');
                $table->text(column: 'queue');
                $table->longText(column: 'payload');
                $table->longText(column: 'exception');

                $table->timestamp(column: 'failed_at')->useCurrent();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'failed_jobs');
    }
};
