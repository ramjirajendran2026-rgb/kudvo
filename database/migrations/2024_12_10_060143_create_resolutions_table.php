<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_ALLOW_ABSTAIN_VOTES = false;

    public function up(): void
    {
        Schema::create(
            table: 'resolutions',
            callback: function (Blueprint $table): void {
                $table->id();

                $table->string(column: 'name');
                $table->longText(column: 'description')->nullable();

                $table->boolean(column: 'allow_abstain_votes')->default(value: self::DEFAULT_ALLOW_ABSTAIN_VOTES);

                $table->string(column: 'for_label')->nullable();
                $table->string(column: 'against_label')->nullable();
                $table->string(column: 'abstain_label')->nullable();

                $table->integer(column: 'sort')->default(value: 1);

                $table->foreignId(column: 'meeting_id')
                    ->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                $table->timestamps();
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'resolutions');
    }
};
