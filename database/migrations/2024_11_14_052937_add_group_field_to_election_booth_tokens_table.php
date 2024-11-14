<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('election_booth_tokens', function (Blueprint $table) {
            $table->string('group')->nullable()
                ->index()
                ->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('election_booth_tokens', function (Blueprint $table) {
            $table->dropColumn('group');
        });
    }
};
