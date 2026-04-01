<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const TABLES = ['members', 'elections', 'nominations', 'meetings', 'surveys'];

    public function up(): void
{
    if (!Schema::hasColumn('nominations', 'branch_id')) {
        Schema::table('nominations', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('organisation_id');
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
