<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const TABLES = ['members', 'elections', 'nominations', 'meetings', 'surveys'];

    public function up(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('organisation_id')
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('branch_id');
            });
        }
    }
};
